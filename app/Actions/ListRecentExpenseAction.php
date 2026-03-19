<?php

namespace App\Actions;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Collection;

class ListRecentExpenseAction
{
    /**
     * Get the most recent expenses for the given user.
     *
     * @return Collection<int, array{id:int,amount:float,category:string,description:string,ai_confidence:float|null}>
     */
    public function __invoke(User $user, int $limit = 10): Collection
    {
        return Expense::query()
            ->forUser($user)
            ->recent($limit)
            ->get(['id', 'amount', 'category', 'description', 'ai_confidence'])
            ->map(fn (Expense $expense): array => [
                'id' => $expense->id,
                'amount' => (float) $expense->amount,
                'category' => $expense->category,
                'description' => $expense->description,
                'ai_confidence' => $expense->ai_confidence === null
                    ? null
                    : (float) $expense->ai_confidence,
            ]);
    }
}
