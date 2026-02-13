<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ url: '' });

const submit = () => {
    form.post(route('settings.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Настройка интеграции" />
    <AuthenticatedLayout>

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Подключение площадок</h2>

        <div class="bg-white p-8 rounded-lg shadow-sm max-w-2xl">
            <div class="flex items-center gap-2 mb-6">
                <span class="text-red-600 font-bold text-xl">Я</span>
                <h3 class="text-lg font-bold">Яндекс Карты</h3>
            </div>

            <p class="text-sm text-gray-500 mb-4">
                Укажите ссылку на организацию в Яндекс Картах, например: <br>
                <span class="text-gray-400 italic">https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/</span>
            </p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <input
                        v-model="form.url"
                        type="text"
                        placeholder="Вставьте ссылку сюда"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <div v-if="form.errors.url" class="text-red-500 text-xs mt-1">{{ form.errors.url }}</div>
                </div>

                <div class="flex items-center gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 transition"
                    >
                        {{ form.processing ? 'Сохранение...' : 'Сохранить' }}
                    </button>

                    <span v-if="form.recentlySuccessful" class="text-green-600 text-sm">Успешно сохранено!</span>
                </div>
            </form>
        </div>

    </AuthenticatedLayout>
</template>
