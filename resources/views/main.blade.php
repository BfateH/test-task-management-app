@extends('layouts.app')

@section('content')
    <h1 class="text-center">Управление задачами + API</h1>

    <div class="alert-container">
        @if(session('error'))
            <div class="error-alert" id="errorAlert">
                <div class="error-alert-content">
                    <div class="error-icon">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <p class="error-message">{!! session('error') !!} </p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="success-alert" id="successAlert">
                <div class="success-alert-content">
                    <div class="success-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <p class="success-message">{!! session('success') !!}</p>
                </div>
            </div>
        @endif
    </div>


    <h2>Задачи @if(request()->has('in_archive')) - Архивные @endif</h2>
    @auth()
        @if($totalTasks)
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div>
                    @if(request()->has('in_archive'))
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2 mb-2">Задачи</a>
                    @else
                        <a href="{{ route('home', ['in_archive' => 1]) }}" class="btn btn-outline-primary me-2 mb-2">Архивные
                            задачи</a>
                    @endif

                    <a href="{{ route('tasks.create') }}" class="btn btn-outline-success me-2 mb-2">Создать задачу</a>
                    <button class="btn btn-outline-primary toggle-filters mb-2" type="button" id="toggleFilters">
                        <span class="btn-text"><i class="bi bi-chevron-down me-1"></i> Показать фильтры</span>
                    </button>
                </div>

                @auth()
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <input type="submit" class="btn btn-outline-danger" value="Выход">
                    </form>
                @endauth

            </div>
            <!-- Индикатор активных фильтров -->
            @if(count(request()->all()) > 0)
                <div class="fw-bold small text-danger">
                    Применены фильтры
                </div>
            @endif

            <div class="filter-section" id="filterSection">
                <h3 class="filter-title"><i class="bi bi-funnel"></i> Фильтры и поиск</h3>

                <form method="GET" action="{{ route('home') }}">
                    @if(request()->has('in_archive'))
                        <input type="hidden" name="in_archive" value="1">
                    @endif
                    <div class="row">
                        <!-- Фильтр по статусу -->
                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="status" class="form-label">Статус</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Все статусы</option>
                                @foreach(App\Enums\TaskStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по исполнителю -->
                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="executor_id" class="form-label">Исполнитель</label>
                            <select class="form-select" id="executor_id" name="executor_id">
                                <option value="" {{ !request('executor_id') ? 'selected' : '' }}>Все исполнители</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('executor_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Фильтр по дате создания -->
                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="created_from" class="form-label">Дата создания от</label>
                            <input type="date" class="form-control" id="created_from" name="created_from"
                                   value="{{ request('created_from') }}">
                        </div>

                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="created_to" class="form-label">Дата создания до</label>
                            <input type="date" class="form-control" id="created_to" name="created_to"
                                   value="{{ request('created_to') }}">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- Фильтр по дате выполнения -->
                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="due_date_from" class="form-label">Дата выполнения от</label>
                            <input type="date" class="form-control" id="due_date_from" name="due_date_from"
                                   value="{{ request('due_date_from') }}">
                        </div>

                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="due_date_to" class="form-label">Дата выполнения до</label>
                            <input type="date" class="form-control" id="due_date_to" name="due_date_to"
                                   value="{{ request('due_date_to') }}">
                        </div>

                        <!-- Фильтр по фактической дате выполнения -->
                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="actual_date_from" class="form-label">Фактическая дата от</label>
                            <input type="date" class="form-control" id="actual_date_from" name="actual_date_from"
                                   value="{{ request('actual_date_from') }}">
                        </div>

                        <div class="col-md-6 col-lg-3 filter-group">
                            <label for="actual_date_to" class="form-label">Фактическая дата до</label>
                            <input type="date" class="form-control" id="actual_date_to" name="actual_date_to"
                                   value="{{ request('actual_date_to') }}">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- Поиск по названию и описанию -->
                        <div class="col-md-12 filter-group">
                            <label for="search" class="form-label">Поиск по названию и описанию</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Введите текст для поиска...">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div class="d-flex flex-wrap">
                                <button type="submit" class="btn btn-primary btn-filter mb-2 me-2">
                                    <i class="bi bi-check-lg"></i> Применить фильтры
                                </button>

                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-filter mb-2 me-2">
                                    <i class="bi bi-x-circle"></i> Сбросить фильтры
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                @foreach($groupedTasks as $status_name => $group)
                    @if($group['tasks']->count())
                        <div class="col-md-4 divider">
                            <div class="kanban-column" data-status="{{ $status_name }}">
                                <h3 class="column-title text-{{ $group['class'] }}">
                                    <i class="bi {{ $group['icon'] }}"></i> {{ $group['label'] }}
                                </h3>

                                @foreach($group['tasks'] as $task)
                                    <div data-task-id="{{ $task->id }}" class="kanban-card card {{ !$task->is_owner && !$task->is_executor ? 'kanban-card-not-owner' : 'kanban-card-owner' }}">
                                        <div class="card-body d-flex flex-column">
                                            @if($task->is_owner)
                                                <div class="owner-badge">
                                                    <i class="bi bi-person-check me-1"></i> Ваша карточка
                                                </div>
                                            @elseif($task->is_executor)
                                                <div class="owner-badge">
                                                    <i class="bi bi-person-check me-1"></i> Вы исполнитель
                                                </div>
                                            @endif

                                            <div class="card-content">
                                                <h5 class="card-title">{{ $task->name }}</h5>
                                                <p class="card-text">{{ $task->description }}</p>
                                                <div class="task-meta mb-2 d-flex align-items-start">
                                                    <div
                                                        class="badge bg-{{ $group['class'] }}">{{ $group['label'] }}</div>
                                                    <div class="d-flex flex-column">
                                                        <span class="ms-2"><i class="bi bi-clock"></i> Дата срока исполнения: {{ $task->due_date->format('Y-m-d') }}</span>
                                                        <span class="ms-2"><i class="bi bi-clock"></i> Фактическая дата исполнения: {{ $task->actual_date_of_execution ? $task->actual_date_of_execution->format('Y-m-d') : 'Дата не определена.' }}</span>
                                                        <span class="ms-2"><i class="bi bi-person"></i> Постановщик: {{ $task->producer->name }}</span>
                                                        <span class="ms-2"><i class="bi bi-person"></i> Исполнитель: {{ $task->executor->name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-auto">
                                                <div>
                                                    @if($task->is_owner)
                                                        <a href="{{ route('tasks.edit', $task->id) }}"
                                                           class="btn btn-outline-primary btn-action me-1"
                                                           title="Редактировать">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form class="d-inline-block" method="post"
                                                              action="{{ route('tasks.delete', $task->id) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger btn-action me-1"
                                                                    title="Удалить">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('tasks.toArchive', $task->id) }}"
                                                           class="btn btn-outline-warning btn-action me-1"
                                                           title="@if(!request()->has('in_archive')) Архивировать @else Убрать из архива @endif">
                                                            <i class="bi bi-file-earmark-zip-fill"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($group['next_action'] && ($task->is_executor || $task->is_owner))
                                                    <a href="{{ route('tasks.nextStatus', $task->id) }}"
                                                       class="btn btn-outline-success btn-action"
                                                       title="{{ $group['next_action']['title'] }}">
                                                        <i class="bi {{ $group['next_action']['icon'] }}"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-md-4 divider">
                            <div data-status="{{ $status_name }}" class="kanban-column">
                                <h3 class="column-title text-{{ $group['class'] }}">
                                    <i class="bi {{ $group['icon'] }}"></i> {{ $group['label'] }}
                                </h3>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="text-center py-5">
                    <div class="py-5">
                        <i class="bi bi-clipboard-check display-1 text-muted"></i>
                        <h2 class="h3 text-muted mt-4">Задач пока нет</h2>
                        <p class="text-muted mb-4">Начните организовывать вашу работу, создав первую задачу</p>
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>Создать первую задачу
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    @guest()
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center">
                    <div class="guest-placeholder">
                        <div class="placeholder-icon mb-4">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <h2 class="mb-3">Доступ ограничен</h2>
                        <p class="text-muted mb-4">Для работы с системой управления задачами необходимо авторизоваться
                            или зарегистрироваться</p>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="bi bi-person-plus me-2"></i>Регистрация
                            </a>
                        </div>

                        <div class="features mt-5 pt-4 border-top">
                            <h4 class="mb-4">Возможности системы</h4>
                            <div class="row">
                                <div class="col-md-4 feature-item mb-4">
                                    <i class="bi bi-kanban fs-1 text-primary mb-2"></i>
                                    <h5>Канбан-доска</h5>
                                    <p class="small">Визуальное управление задачами</p>
                                </div>
                                <div class="col-md-4 feature-item mb-4">
                                    <i class="bi bi-funnel fs-1 text-primary mb-2"></i>
                                    <h5>Фильтрация</h5>
                                    <p class="small">Гибкие настройки отображения</p>
                                </div>
                                <div class="col-md-4 feature-item mb-4">
                                    <i class="bi bi-people fs-1 text-primary mb-2"></i>
                                    <h5>Командная работа</h5>
                                    <p class="small">Назначение задач исполнителям</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest
@endsection
