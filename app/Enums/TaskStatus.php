<?php

namespace App\Enums;

enum TaskStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Новая',
            self::IN_PROGRESS => 'В работе',
            self::COMPLETED => 'Завершена',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function nextStatus(): ?self
    {
        return match($this) {
            self::NEW => self::IN_PROGRESS,
            self::IN_PROGRESS => self::COMPLETED,
            self::COMPLETED => null,
        };
    }


}
