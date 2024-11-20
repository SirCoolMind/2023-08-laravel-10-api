<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MoneySubcategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('money_subcategories')->delete();
        
        \DB::table('money_subcategories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'money_category_id' => 1,
                'name' => 'Rent Related',
                'description' => 'For rent room',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:49:06',
                'updated_at' => '2024-11-17 16:49:06',
            ),
            1 => 
            array (
                'id' => 2,
                'money_category_id' => 1,
                'name' => 'Home Related',
                'description' => 'For family home',
                'user_id' => 1,
                'created_at' => '2024-11-17 16:49:06',
                'updated_at' => '2024-11-17 16:49:06',
            ),
            2 => 
            array (
                'id' => 3,
                'money_category_id' => 1,
                'name' => 'Monthly Bills',
                'description' => 'Monthly bills',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            3 => 
            array (
                'id' => 4,
                'money_category_id' => 2,
                'name' => 'Digital Wallet',
                'description' => 'Setel etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            4 => 
            array (
                'id' => 5,
                'money_category_id' => 2,
                'name' => 'Fuel',
                'description' => 'Fuel using cash or card',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            5 => 
            array (
                'id' => 6,
                'money_category_id' => 2,
                'name' => 'Maintenance',
                'description' => 'Oil change or repair',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            6 => 
            array (
                'id' => 7,
                'money_category_id' => 2,
                'name' => 'Public Transport',
                'description' => 'Ticket etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            7 => 
            array (
                'id' => 8,
                'money_category_id' => 3,
                'name' => 'Groceries',
                'description' => 'Self - food stuff etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            8 => 
            array (
                'id' => 9,
                'money_category_id' => 3,
                'name' => 'Family',
                'description' => 'Treat family, groceries',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            9 => 
            array (
                'id' => 10,
                'money_category_id' => 3,
                'name' => 'Dine Out',
                'description' => 'Eat outside',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            10 => 
            array (
                'id' => 11,
                'money_category_id' => 4,
                'name' => 'Health',
                'description' => 'Insurance for health',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            11 => 
            array (
                'id' => 12,
                'money_category_id' => 4,
                'name' => 'Life',
                'description' => 'Insurance for life',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            12 => 
            array (
                'id' => 13,
                'money_category_id' => 5,
                'name' => 'Credit Card',
                'description' => 'Big purchase',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            13 => 
            array (
                'id' => 14,
                'money_category_id' => 5,
                'name' => 'Loan',
                'description' => 'SPayLater, personal loan etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            14 => 
            array (
                'id' => 15,
                'money_category_id' => 5,
                'name' => 'Mortgage',
                'description' => 'House',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            15 => 
            array (
                'id' => 16,
                'money_category_id' => 6,
                'name' => 'Sport',
                'description' => 'Equipment, gym etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            16 => 
            array (
                'id' => 17,
                'money_category_id' => 6,
                'name' => 'Medical Expenses',
                'description' => 'Treatment',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            17 => 
            array (
                'id' => 18,
                'money_category_id' => 6,
                'name' => 'Wellness Programs',
                'description' => 'Healthy community',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            18 => 
            array (
                'id' => 19,
                'money_category_id' => 6,
                'name' => 'Personal Care',
                'description' => 'Hygiene, skincare etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            19 => 
            array (
                'id' => 20,
                'money_category_id' => 7,
                'name' => 'Leisure',
                'description' => 'Cost during outing, leisure',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            20 => 
            array (
                'id' => 21,
                'money_category_id' => 7,
                'name' => 'Hobbies',
                'description' => 'Item collector etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            21 => 
            array (
                'id' => 22,
                'money_category_id' => 8,
                'name' => 'Emergency Funds',
                'description' => 'Unexpected expenses etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            22 => 
            array (
                'id' => 23,
                'money_category_id' => 8,
                'name' => 'Retirement Savings',
                'description' => 'Unexpected expenses etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
            23 => 
            array (
                'id' => 24,
                'money_category_id' => 8,
                'name' => 'Long Term saving goals',
                'description' => 'Unexpected expenses etc.',
                'user_id' => 1,
                'created_at' => '2024-11-17 17:19:25',
                'updated_at' => '2024-11-17 17:19:25',
            ),
        ));
        
        
    }
}