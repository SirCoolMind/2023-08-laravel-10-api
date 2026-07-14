<?php

namespace Tests\Feature\Finance;

use HafizRuslan\Finance\app\Models\MoneyAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoneyAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_money_account()
    {
        $account = new MoneyAccount();
        $account->name = 'Test Bank';
        $account->description = 'A test bank account';
        $account->user_id = 1;
        $account->save();

        $this->assertDatabaseHas('money_accounts', [
            'name' => 'Test Bank',
            'description' => 'A test bank account',
        ]);
    }
}
