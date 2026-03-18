<?php

namespace App\Actions;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListRecentExpensesAction
{
    /**
     * Get the most recent expenses for the given user.
     *
     * @return Collection<int, Expense>
     */
    public function __invoke(User $user, int $limit = 10): Collection
    {
        return Expense::query()
            ->forUser($user)
            ->recent($limit)
            ->get();
    }
}
