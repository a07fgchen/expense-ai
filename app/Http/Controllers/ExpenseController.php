<?php

namespace App\Http\Controllers;

use App\Actions\ListRecentExpenseAction;
use App\Actions\StoreExpenseFromMessage;
use App\Ai\Agents\ExpenseParser;
use App\Http\Requests\ExpenseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request, ListRecentExpenseAction $listRecentExpensesAction): Response
    {
        return Inertia::render('Expense', [
            'expenses' => $listRecentExpensesAction($request->user()),
        ]);
    }

    public function store(
        ExpenseRequest $request,
        ExpenseParser $parser,
        StoreExpenseFromMessage $storeExpenseFromMessage,
    ): RedirectResponse {
        $validated = $request->validated();

        $storeExpenseFromMessage($request->user(), $validated['message'], $parser);

        return back()->with('success', '紀錄成功!');
    }
}
