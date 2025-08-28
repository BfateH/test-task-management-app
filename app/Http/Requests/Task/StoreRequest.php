<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'producer_id' => [
                'required',
                Rule::in([Auth::id()]),
            ],
            'executor_id' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'status' => [
                'sometimes',
                'required',
                Rule::enum(TaskStatus::class)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название задачи обязательно',
            'description.required' => 'Описание задачи обязательно',
            'producer_id.required' => 'Постановщик задачи обязателен',
            'producer_id.exists' => 'Постановщик задачи обязательно должен существовать в базе данных',
            'executor_id.required' => 'Исполнитель задачи обязателен',
            'executor_id.exists' => 'Исполнитель задачи обязательно должен существовать в базе данных',
            'due_date.required' => 'Планируемая дата завершения обязательный параметр',
            'due_date.date' => 'Планируемая дата завершения обязательно должна быть датой',
        ];
    }
}
