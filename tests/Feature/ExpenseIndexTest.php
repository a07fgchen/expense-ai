<?php

use App\Models\Expense;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('expense index only returns current user records with whitelisted fields', function () {
    $currentUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownExpense = Expense::query()->create([
        'user_id' => $currentUser->id,
        'amount' => 123.45,
        'category' => '飲食',
        'description' => '早餐',
        'raw_text' => '早餐 123.45',
        'ai_confidence' => 0.91,
        'ai_model' => 'gemini-3.1-flash',
    ]);

    Expense::query()->create([
        'user_id' => $otherUser->id,
        'amount' => 999.99,
        'category' => '娛樂',
        'description' => '電影',
        'raw_text' => '電影 999.99',
        'ai_confidence' => 0.8,
        'ai_model' => 'gemini-3.1-flash',
    ]);

    $response = $this->actingAs($currentUser)->get(route('expense.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Expense')
        ->has('expenses', 1)
        ->has('expenses.0', fn (Assert $expense) => $expense
            ->where('id', $ownExpense->id)
            ->where('amount', 123.45)
            ->where('category', '飲食')
            ->where('description', '早餐')
            ->where('ai_confidence', 0.91)
            ->missing('raw_text')
            ->missing('ai_model')
            ->etc()
        )
    );
});
