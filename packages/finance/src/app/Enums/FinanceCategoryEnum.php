<?php

namespace HafizRuslan\Finance\app\Enums;

enum FinanceCategoryEnum: string
{
    case UTILITIES = 'UTILITIES';
    case FOOD = 'FOOD';
    case ENTERTAINMENT = 'ENTERTAINMENT';
    case TRANSPORTATION = 'TRANSPORTATION';
    case FAMILY = 'FAMILY';

    public function label(): string
    {
        return match ($this) {
            self::UTILITIES      => 'Utilities',
            self::FOOD           => 'Food',
            self::ENTERTAINMENT  => 'Entertainment',
            self::TRANSPORTATION => 'Transportation',
            self::FAMILY         => 'Family',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::UTILITIES      => 'Bill related',
            self::FOOD           => 'Food ate by me',
            self::ENTERTAINMENT  => 'Fun things',
            self::TRANSPORTATION => 'Vroom vrroom!',
            self::FAMILY         => 'mom, dad, siblings cost',
        };
    }

    public function subcategories(): array
    {
        return match ($this) {
            self::UTILITIES      => [
                ['id' => 'monthly_bills', 'name' => 'Monthly Bills'],
                ['id' => 'aircond', 'name' => 'Aircond'],
                ['id' => 'rent_electric', 'name' => 'Rent Electric'],
                ['id' => 'home_electric', 'name' => 'Home Electric'],
            ],
            self::FAMILY         => [
                ['id' => 'grocery', 'name' => 'Grocery'],
                ['id' => 'pocket_money', 'name' => 'Pocket Money'],
            ],
            self::FOOD           => [
                ['id' => 'dining_out', 'name' => 'Dining Out'],
                ['id' => 'home_cooking', 'name' => 'Home Cooking'],
            ],
            self::ENTERTAINMENT  => [
                ['id' => 'movies', 'name' => 'Movies'],
                ['id' => 'games', 'name' => 'Games'],
            ],
            self::TRANSPORTATION => [
                ['id' => 'fuel', 'name' => 'Fuel'],
                ['id' => 'public_transport', 'name' => 'Public Transport'],
            ],
        };
    }
}
