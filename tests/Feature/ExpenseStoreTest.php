<?php

use App\Ai\Agents\ExpenseParser;
use App\Models\Expense;
use App\Models\User;
use Laravel\Ai\Prompts\AgentPrompt;

test('authenticated user can store an expense from AI structured output', function () {
    $user = User::factory()->create();

    ExpenseParser::fake([
        [
            'amount' => 250,
            'category' => '飲食',
            'description' => '中午拉麵',
            'confidence' => 0.92,
        ],
    ]);

    $response = $this->actingAs($user)->post(route('expenses.store'), [
        'message' => '中午吃拉麵花了 250 元',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', '紀錄成功!');

    ExpenseParser::assertPrompted(fn (AgentPrompt $prompt) => $prompt->contains('中午吃拉麵花了 250 元'));

    $expense = Expense::query()->first();

    expect($expense)->not->toBeNull()
        ->and($expense->user_id)->toBe($user->id)
        ->and((float) $expense->amount)->toBe(250.0)
        ->and($expense->category)->toBe('飲食')
        ->and($expense->description)->toBe('中午拉麵')
        ->and((float) $expense->ai_confidence)->toBe(0.92);
});
