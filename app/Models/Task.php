<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort',
        'description',
        'producer_id',
        'executor_id',
        'status',
        'due_date',
        'actual_date_of_execution',
        'in_archive',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due_date' => 'datetime',
        'actual_date_of_execution' => 'datetime',
        'in_archive' => 'boolean',
    ];

    public function producer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function executor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'executor_id');
    }
}
