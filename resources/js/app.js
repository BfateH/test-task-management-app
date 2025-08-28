import './bootstrap';
import '../css/app.css';



function dismissAlert() {
    const alert = document.getElementById('successAlert');
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(100px)';
    setTimeout(() => {
        alert.style.display = 'none';
    }, 500);
}

function showErrorAlert(message) {

    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '1050';
        alertContainer.style.minWidth = '350px';
        alertContainer.style.maxWidth = '500px';
        document.body.appendChild(alertContainer);
    }

    const alertId = 'errorAlert-' + Date.now();
    const alertElement = document.createElement('div');
    alertElement.id = alertId;
    alertElement.className = 'error-alert';
    alertElement.innerHTML = `
        <div class="error-alert-content">
            <div class="error-icon">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <p class="error-message">${message}</p>
        </div>
    `;

    alertContainer.appendChild(alertElement);

    setTimeout(() => {
        alertElement.style.opacity = '1';
        alertElement.style.transform = 'translateY(0)';
    }, 10);

    setTimeout(() => {
        dismissErrorAlert(alertId);
    }, 5000);
}

function dismissErrorAlert(alertId = null) {
    const alert = document.getElementById(alertId ? alertId :'errorAlert');
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(100px)';
    setTimeout(() => {
        alert.style.display = 'none';
    }, 500);
}

document.addEventListener('DOMContentLoaded', function() {
    const kanbanColumns = document.querySelectorAll('.kanban-column');

    kanbanColumns.forEach(column => {
        new Sortable(column, {
            group: 'shared',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            draggable: '.kanban-card',
            onEnd: function (evt) {
                const item = evt.item;
                const movedTaskId = item.dataset.taskId;
                const newColumn = evt.to.closest('.kanban-column');
                const newStatus = newColumn.dataset.status;

                const originalColumn = evt.from;
                const originalIndex = evt.oldIndex;

                // Новый индекс в новой колонке
                const newIndex = Array.from(newColumn.querySelectorAll('.kanban-card')).indexOf(item);

                // Все карточки в новой колонке
                const allCards = newColumn.querySelectorAll('.kanban-card');

                // ID задач, идущих ПОСЛЕ перемещённой
                const tasksAfterMoved = Array.from(allCards)
                    .slice(newIndex + 1)
                    .map(card => card.dataset.taskId);

                // Все задачи в колонке с индексами
                const allTasksWithIndex = Array.from(allCards).map((card, index) => ({
                    id: card.dataset.taskId,
                    sort: index
                }));

                // Формируем payload
                const payload = {
                    status: newStatus,
                    tasks: allTasksWithIndex,
                };

                // Отправляем на сервер
                fetch(`/changeStatus/${movedTaskId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Сетевая ошибка: ' + response.status);
                        return response.json();
                    })
                    .then(data => {
                        if (!data.data?.success) {
                            const errorMsg = data.data?.error || 'Ошибка обновления';
                            showErrorAlert(errorMsg);
                            revertCardMove(item, originalColumn, originalIndex);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка в fetch:', error);
                        showErrorAlert('Ошибка сети при обновлении задачи');
                        revertCardMove(item, originalColumn, originalIndex);
                    });
            }
        });
    });

    function revertCardMove(card, originalColumn, originalIndex) {
        if (card.parentNode) {
            card.parentNode.removeChild(card);
        }

        const children = Array.from(originalColumn.children);

        if (originalIndex >= children.length) {
            originalColumn.appendChild(card);
        } else {
            originalColumn.insertBefore(card, children[originalIndex]);
        }

        card.style.transition = 'all 0.3s ease';
        card.style.boxShadow = '0 0 10px rgba(220, 53, 69, 0.5)';

        setTimeout(() => {
            card.style.boxShadow = '';
            card.style.transition = '';
        }, 300);
    }



    const errorAlert = document.getElementById('errorAlert');
    if (errorAlert) {
        setTimeout(dismissErrorAlert, 5000);
    }

    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(dismissAlert, 5000);
    }

    const filterSection = document.getElementById('filterSection');
    const toggleButton = document.getElementById('toggleFilters');
    let btnText = null;

    if (toggleButton) {
        btnText = toggleButton.querySelector('.btn-text');
    }

    let isFiltersVisible = false;

    function toggleFilters() {
        isFiltersVisible = !isFiltersVisible;

        if (isFiltersVisible) {
            filterSection.classList.add('show');
            btnText.innerHTML = `
                    <i class="bi bi-chevron-up me-1"></i>
                    <span class="btn-text">Скрыть фильтры</span>
                `;
            toggleButton.classList.add('collapsed');
        } else {
            filterSection.classList.remove('show');
            btnText.innerHTML = `
                    <i class="bi bi-chevron-down me-1"></i>
                    <span class="btn-text">Показать фильтры</span>
                `;
            toggleButton.classList.remove('collapsed');
        }
    }

    // Обработчик клика по кнопке
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleFilters);
    }
});
