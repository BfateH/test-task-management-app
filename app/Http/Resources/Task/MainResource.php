<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'due_date' => $this->due_date->format('Y-m-d'),
            'actual_date_of_execution' => $this->actual_date_of_execution ? $this->actual_date_of_execution->format('Y-m-d') : null,
            'in_archive' => (bool)$this->in_archive,
            'sort' => $this->sort,
            'producer' => new \App\Http\Resources\User\MainResource($this->whenLoaded('producer')),
            'executor' => new \App\Http\Resources\User\MainResource($this->whenLoaded('executor')),

            'can_edit' => $request->user() ? $request->user()->can('update', $this->resource) : false,
            'can_delete' => $request->user() ? $request->user()->can('delete', $this->resource) : false,
            'can_change_status' => $request->user() ? $request->user()->can('changeStatus', $this->resource) : false,
            'can_archive' => $request->user() ? $request->user()->can('toArchive', $this->resource) : false,

            'is_owner' => $request->user() ? $request->user()->id === $this->producer_id : false,
            'is_executor' => $request->user() ? $request->user()->id === $this->executor_id : false,
        ];
    }
}
