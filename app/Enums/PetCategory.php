<?php

namespace App\Enums;

enum PetCategory: string
{
    case DOGS = 'Dogs';
    case CATS = 'Cats';
    case BIRDS = 'Birds';
    case FISH = 'Fish';
    case RABBITS = 'Rabbits';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::DOGS => 'Psy',
            self::CATS => 'Koty',
            self::BIRDS => 'Ptaki',
            self::FISH => 'Ryby',
            self::RABBITS => 'Króliki',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ], self::cases());
    }
}
