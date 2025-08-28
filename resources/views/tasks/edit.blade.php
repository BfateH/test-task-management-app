@extends('layouts.app')

@section('content')
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <div class="form-header">
                        <h2><i class="bi bi-plus-circle me-2"></i>Редактирование задачи {{ $task->name }}</h2>
                        <p class="text-muted">Заполните информацию о задаче</p>
                    </div>

                    <form method="post" action="{{ route('tasks.update', $task->id) }}">
                        @csrf
                        @method('patch')

                        <div class="form-section">
                            <h4 class="form-section-title"><i class="bi bi-info-circle me-2"></i>Основная информация
                            </h4>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label required-field">Название задачи</label>
                                    <input value="{{ old('name') ? old('name') : $task->name }}" type="text"
                                           class="form-control @error('name') is-invalid @enderror" id="name"
                                           name="name" required>
                                    <div class="form-text">Краткое и понятное название задачи</div>
                                    @error('name')
                                    <div class="text-danger fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label required-field">Описание</label>
                                <textarea required class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4"
                                          placeholder="Подробное описание задачи...">{{ old('description') ? old('description') : $task->description }}</textarea>
                                <div class="form-text">Опишите детали и требования к задаче</div>
                                @error('description')
                                <div class="text-danger fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="form-section-title"><i class="bi bi-people me-2"></i>Ответственные лица</h4>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="producer_id" class="form-label required-field">Постановщик</label>
                                    <select class="form-select @error('producer_id') is-invalid @enderror" id="producer_id" name="producer_id" required>
                                        <option value="{{ auth()->user()->id }}"
                                                selected>{{ auth()->user()->name }}</option>
                                    </select>
                                    @error('producer_id')
                                    <div class="text-danger fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="executor_id" class="form-label required-field">Исполнитель</label>
                                    <select class="form-select @error('executor_id') is-invalid @enderror" id="executor_id" name="executor_id" required>
                                        <option value="" selected disabled>Выберите исполнителя</option>
                                        @foreach($users as $user)
                                            @php
                                                $selected = false;
                                                if (old('executor_id') !== null) {
                                                    $selected = (int) old('executor_id') === (int) $user->id;
                                                } else {
                                                    $selected = isset($task) && (int) $task->executor_id === (int) $user->id;
                                                }
                                            @endphp

                                            <option value="{{ $user->id }}" {{ $selected ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('executor_id')
                                    <div class="text-danger fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="form-section-title"><i class="bi bi-calendar-event me-2"></i>Сроки выполнения
                            </h4>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="due_date" class="form-label required-field">Планируемая дата
                                        завершения</label>
                                    <input
                                        value="{{ old('due_date') ? old('due_date') : $task->due_date->format('Y-m-d')  }}"
                                        type="date" class="form-control @error('due_date') is-invalid @enderror"
                                        id="due_date" name="due_date" required>
                                    @error('due_date')
                                    <div class="text-danger fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary d-flex align-items-center">
                                <i class="bi bi-arrow-left me-2"></i>Назад
                            </a>

                            <div>
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="bi bi-check-circle me-2"></i>Обновить задачу
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
