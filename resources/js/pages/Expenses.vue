<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';

// 使用 Inertia 的 useForm 處理 AI 請求
const form = useForm({
    message: '',
});

// 處理送出邏輯
const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => {
            form.reset('message'); // 成功後清空輸入框
        },
    });
};

const getCategoryClass = (category) => {
    const map = {
        '飲食': 'bg-orange-100 text-orange-700',
        '交通': 'bg-blue-100 text-blue-700',
        '娛樂': 'bg-purple-100 text-purple-700',
        '生活': 'bg-green-100 text-green-700',
    };
    return map[category] || 'bg-gray-100 text-gray-700';
};
</script>

<template>

    <Head title="AI 記帳助手" />

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">🤖 Gemini AI 記帳</h2>
                <form @submit.prevent="submit" class="relative">
                    <input v-model="form.message" type="text" placeholder="例如：中午吃拉麵花了 250 元"
                        class="w-full border-gray-300 rounded-full pr-12 focus:ring-blue-500 transition-all"
                        :disabled="form.processing" />
                    <button type="submit"
                        class="absolute right-2 top-1.5 bg-blue-600 text-white p-1.5 rounded-full hover:bg-blue-700 transition-all"
                        :class="{ 'opacity-50': form.processing }">
                        <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span v-else class="animate-spin text-xs">...</span>
                    </button>
                </form>
                <p v-if="form.processing" class="text-sm text-blue-500 mt-2">Gemini 正在解析您的消費...</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">最近紀錄</h3>
                <div v-for="expense in $page.props.expenses" :key="expense.id"
                    class="border-b py-3 flex justify-between items-center">
                    <div>
                        <span class="px-2 py-1 rounded-md text-xs font-bold mr-2"
                            :class="getCategoryClass(expense.category)">
                            {{ expense.category }}
                        </span>
                        <span class="text-gray-800">{{ expense.description }}</span>
                    </div>
                    <div class="text-right">
                        <p class="font-mono font-bold text-lg">${{ expense.amount }}</p>
                        <p class="text-[10px] text-gray-400">AI 信心指數: {{ (expense.ai_confidence * 100).toFixed(0) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
