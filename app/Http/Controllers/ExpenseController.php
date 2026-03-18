<?php

namespace App\Http\Controllers;

use App\Ai\Agents\ExpenseParser;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    //
    public function store(Request $request, ExpenseParser $parser)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $result = $parser->prompt($request->input('message'));

        Expense::create([
            'user_id' => Auth::id(),
            'amount' => $result['amount'],
            'category' => $result['category'],
            'description' => $result['description'],
            'ai_confidence' => $result['confidence'],
            'ai_model' => 'gemini-3.1-flash',
        ]);

        return back()->with('success','紀錄成功!');
    }
}
