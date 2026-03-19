<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/routes/expense';
import type { BreadcrumbItem } from '@/types';

type ExpenseItem = {
    id: number;
    amount: number;
    category: string;
    description: string;
    ai_confidence: number | null;
};

defineProps<{
    expenses: ExpenseItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Expense',
        href: index(),
    },
];

const form = useForm({
    message: '',
});

const submit = (): void => {
    form.submit(store(), {
        onSuccess: () => {
            form.reset('message');
        },
    });
};

const getCategoryClass = (category: string): string => {
    const categoryColorMap: Record<string, string> = {
        飲食: 'bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-200',
        交通: 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-200',
        娛樂: 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-500/20 dark:text-fuchsia-200',
        生活: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
    };

    return categoryColorMap[category] ?? 'bg-muted text-muted-foreground';
};
</script>

<template>
    <Head title="AI 記帳助手" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col gap-6 p-4 md:p-6">
            <section class="rounded-xl border bg-card p-5 text-card-foreground shadow-sm">
                <h2 class="mb-4 text-xl font-semibold">🤖 Gemini AI 記帳</h2>

                <form class="flex items-center gap-3" @submit.prevent="submit">
                    <Input
                        v-model="form.message"
                        type="text"
                        placeholder="例如：中午吃拉麵花了 250 元"
                        class="h-11 flex-1"
                        :disabled="form.processing"
                    />

                    <Button
                        type="submit"
                        size="icon"
                        class="h-11 w-11 rounded-full"
                        :disabled="form.processing"
                    >
                        <svg
                            v-if="!form.processing"
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        <span v-else class="text-xs">...</span>
                    </Button>
                </form>

                <p v-if="form.processing" class="mt-2 text-sm text-muted-foreground">
                    Gemini 正在解析您的消費...
                </p>
            </section>

            <section class="rounded-xl border bg-card p-5 text-card-foreground shadow-sm">
                <h3 class="mb-4 text-base font-semibold">最近紀錄</h3>

                <div v-if="expenses.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                    目前還沒有紀錄，先輸入一筆消費試試看。
                </div>

                <div v-else>
                    <div
                        v-for="expense in expenses"
                        :key="expense.id"
                        class="flex items-center justify-between border-b py-3 last:border-b-0"
                    >
                        <div class="min-w-0">
                            <span
                                class="mr-2 inline-flex rounded-md px-2 py-1 text-xs font-semibold"
                                :class="getCategoryClass(expense.category)"
                            >
                                {{ expense.category }}
                            </span>
                            <span class="truncate align-middle">{{ expense.description }}</span>
                        </div>

                        <div class="text-right">
                            <p class="font-mono text-lg font-bold">${{ expense.amount.toFixed(2) }}</p>
                            <p class="text-[10px] text-muted-foreground">
                                AI 信心指數:
                                {{ expense.ai_confidence === null ? '--' : `${(expense.ai_confidence * 100).toFixed(0)}%` }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
