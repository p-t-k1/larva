<?php

namespace App\Enums;

enum PetStatus: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Dostępny',
            self::PENDING => 'W trakcie adopcji',
            self::SOLD => 'Adoptowany',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
