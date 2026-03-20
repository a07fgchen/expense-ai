<?php

namespace App\Actions;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListRecentExpenseAction
{
    /**
     * Get a paginated list of expenses for the given user, shaped as a DTO.
     *
     * @return LengthAwarePaginator<array{id:int,amount:float,category:string,description:string,ai_confidence:float|null}>
     */
    public function __invoke(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Expense::query()
            ->forUser($user)
            ->latest()
            ->paginate($perPage, ['id', 'amount', 'category', 'description', 'ai_confidence'])
            ->withQueryString()
            ->through(fn (Expense $expense): array => [
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
