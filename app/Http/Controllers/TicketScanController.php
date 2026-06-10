<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class TicketScanController extends Controller
{
   public function store(Request $request, Budget $Budget){
   $request->validate([
    "image"=> ['required', 'image', 'max:1240'],
   ]);
 
   }
}
