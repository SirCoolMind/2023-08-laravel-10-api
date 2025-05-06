<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MoneyCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('money_categories')->delete();

        \DB::table('money_categories')->insert(array (
            0 =>
            array (
                'id' => 1,
                'type' => 'EXPENSE',
                'name' => 'Utility',
                'description' => 'Bill related',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            1 =>
            array (
                'id' => 2,
                'type' => 'EXPENSE',
                'name' => 'Transportation',
                'description' => 'Vroom vroom',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            2 =>
            array (
                'id' => 3,
                'type' => 'EXPENSE',
                'name' => 'Food',
                'description' => 'Anything you eat',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            3 =>
            array (
                'id' => 4,
                'type' => 'EXPENSE',
                'name' => 'Insurance',
                'description' => 'Monthly Insurance',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            4 =>
            array (
                'id' => 5,
                'type' => 'EXPENSE',
                'name' => 'Debt',
                'description' => 'Expense loan',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            5 =>
            array (
                'id' => 6,
                'type' => 'EXPENSE',
                'name' => 'Health & Fitness',
                'description' => 'Strong body, strong mind',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            6 =>
            array (
                'id' => 7,
                'type' => 'EXPENSE',
                'name' => 'Entertainment',
                'description' => 'Fun',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            7 =>
            array (
                'id' => 8,
                'type' => 'EXPENSE',
                'name' => 'Saving',
                'description' => 'Financial security',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            8 =>
            array (
                'id' => 9,
                'type' => 'INCOME',
                'name' => 'Salary',
                'description' => 'Salary by company',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:45:54',
                'updated_at' => '2024-11-17 16:45:54',
            ),
            9 =>
            array (
                'id' => 10,
                'type' => 'INCOME',
                'name' => 'Offset',
                'description' => 'offset',
                'user_id' => 1,
                'created_at' => '2025-02-26 08:30:42',
                'updated_at' => '2025-02-26 08:30:42',
            ),
        ));


    }
}
