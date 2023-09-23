<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

use App\Http\Controllers\Controller;

class LookupController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Lookup/Retrieve Data Controller
    |--------------------------------------------------------------------------
    |
    | Method of retrieving data can be vary. You can either customize, loop, add new array
    | or just call the existing listing inside controller
    |
    */

    public function transactionType()
    {
        $controller = new \App\Http\Controllers\Api\V1\Transaction\TransactionType\TransactionTypeController;
        return $controller->index();

    }
}
