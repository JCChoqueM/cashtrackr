<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{


  
    public function store(ExpenseRequest $request)
    {
        \dd('desde store');
    }
    
    public function update(Request $request, Expense $expense)
    {
        //
    }

    
    public function destroy(Expense $expense)
    {
        //
    }
}
