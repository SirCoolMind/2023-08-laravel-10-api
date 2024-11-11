<?php

namespace HafizRuslan\Finance\app\Enums;

enum FinanceCategoryEnum: string
{
    case UTILITIES = 'UTILITIES';
    case FOOD = 'FOOD';
    case ENTERTAINMENT = 'ENTERTAINMENT';
    case TRANSPORTATION = 'TRANSPORTATION';
    case FAMILY = 'FAMILY';

    public function description(): string
    {
        return match($this)
        {
            self::UTILITIES => 'Bill related',
            self::FOOD => 'Food ate by me',
            self::ENTERTAINMENT => 'Fun things',
            self::TRANSPORTATION => 'Vroom vrroom!',
            self::FAMILY => 'mom, dad, siblings cost',
        };
    }
}
