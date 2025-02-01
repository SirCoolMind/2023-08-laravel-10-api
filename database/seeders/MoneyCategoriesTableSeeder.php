<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MoneyCategoriesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('money_categories')->delete();

        \DB::table('money_categories')->insert([
            0 => [
                'id'          => 1,
                'name'        => 'Utility',
                'description' => 'Bill related',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            1 => [
                'id'          => 2,
                'name'        => 'Transportation',
                'description' => 'Vroom vroom',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            2 => [
                'id'          => 3,
                'name'        => 'Food',
                'description' => 'Anything you eat',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            3 => [
                'id'          => 4,
                'name'        => 'Insurance',
                'description' => 'Monthly Insurance',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            4 => [
                'id'          => 5,
                'name'        => 'Debt',
                'description' => 'Expense loan',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            5 => [
                'id'          => 6,
                'name'        => 'Health & Fitness',
                'description' => 'Strong body, strong mind',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            6 => [
                'id'          => 7,
                'name'        => 'Entertainment',
                'description' => 'Fun',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            7 => [
                'id'          => 8,
                'name'        => 'Saving',
                'description' => 'Financial security',
                'type'        => 'EXPENSE',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
            8 => [
                'id'          => 9,
                'name'        => 'Salary',
                'description' => 'Salary by company',
                'type'        => 'INCOME',
                'user_id'     => 1,
                'created_at'  => '2024-11-17 16:45:54',
                'updated_at'  => '2024-11-17 16:45:54',
            ],
        ]);
    }
}
