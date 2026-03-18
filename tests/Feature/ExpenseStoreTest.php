<?php

use App\Ai\Agents\ExpenseParser;
use App\Models\Expense;
use App\Models\User;
use Laravel\Ai\Prompts\AgentPrompt;

test('已認證用戶可以從AI結構化輸出記錄消費', function () {
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
        ->and($expense->raw_text)->toBe('中午吃拉麵花了 250 元')
        ->and($expense->ai_model)->toBe('gemini-3.1-flash')
        ->and((float) $expense->ai_confidence)->toBe(0.92);
});

test('訪客嘗試記錄消費時會被重導到登入頁面', function () {
    $response = $this->post(route('expenses.store'), [
        'message' => '晚餐便當 120 元',
    ]);

    $response->assertRedirect(route('login'));

    expect(Expense::query()->count())->toBe(0);
});

test('expense message 是必填的', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('expenses.index'))->post(route('expenses.store'), [
        'message' => '',
    ]);

    $response->assertRedirect(route('expenses.index'));
    $response->assertSessionHasErrors([
        'message' => '請輸入消費紀錄',
    ]);

    expect(Expense::query()->count())->toBe(0);
});

test('當AI信心度超出有效範圍時無法生成紀錄', function () {
    $user = User::factory()->create();

    ExpenseParser::fake([
        [
            'amount' => 250,
            'category' => '投資',
            'description' => '中午拉麵',
            'confidence' => 1.2,
        ],
    ]);

    $response = $this->actingAs($user)->from(route('expenses.index'))->post(route('expenses.store'), [
        'message' => '中午吃拉麵花了 250 元',
    ]);

    $response->assertRedirect(route('expenses.index'));
    $response->assertSessionHasErrors([
        'message' => 'AI 無法穩定解析這筆消費，請換個方式描述。',
    ]);

    expect(Expense::query()->count())->toBe(0);
});
