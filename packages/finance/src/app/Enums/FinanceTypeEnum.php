<?php

namespace HafizRuslan\Finance\app\Enums;

enum FinanceTypeEnum: string
{
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public function label(): string
    {
        return match ($this) {
            self::INCOME    => 'Income',
            self::EXPENSE   => 'Expense',
        };
    }
}
