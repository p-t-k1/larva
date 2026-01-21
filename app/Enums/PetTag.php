<?php

namespace App\Enums;

enum PetTag: string
{
    case FRIENDLY = 'friendly';
    case PLAYFUL = 'playful';
    case YOUNG = 'young';
    case TRAINED = 'trained';
    case VACCINATED = 'vaccinated';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::FRIENDLY => 'Przyjazny',
            self::PLAYFUL => 'Zabawny',
            self::YOUNG => 'Młody',
            self::TRAINED => 'Wyszkolony',
            self::VACCINATED => 'Zaszczepiony',
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
