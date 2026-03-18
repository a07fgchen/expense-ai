<?php

namespace App\Actions;

use App\Ai\Agents\ExpenseParser;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreExpenseFromMessage
{
    /**
     * Validate the AI response and persist the expense.
     */
    public function __invoke(User $user, string $message, ExpenseParser $parser): Expense
    {
        $result = $parser->prompt($message);
        $payload = $result instanceof Arrayable ? $result->toArray() : (is_array($result) ? $result : []);

        $validator = Validator::make(
            $payload,
            [
                'amount' => ['required', 'numeric', 'gt:0'],
                'category' => ['required', 'string', Rule::in(Expense::CATEGORIES)],
                'description' => ['required', 'string', 'max:255'],
                'confidence' => ['required', 'numeric', 'between:0,1'],
            ],
        );

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'message' => 'AI 無法穩定解析這筆消費，請換個方式描述。',
            ]);
        }

        $validated = $validator->validated();

        return Expense::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'raw_text' => $message,
            'ai_confidence' => $validated['confidence'],
            'ai_model' => 'gemini-3.1-flash',
        ]);
    }
}
