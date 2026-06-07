<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{


  
    public function store(Request $request)
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
