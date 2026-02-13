<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">

        <aside class="w-64 bg-white shadow-md flex flex-col fixed h-full z-10">
            <div class="h-16 flex items-center px-6 border-b">
                <span class="text-xl font-bold text-blue-600">Daily Grow</span>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <div class="text-xs font-semibold text-gray-400 uppercase mb-2">Название аккаунта</div>

                <Link
                    :href="route('reviews.index')"
                    class="flex items-center px-4 py-2 rounded-lg transition-colors"
                    :class="route().current('reviews.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                >
                    <span class="mr-3">💬</span> Отзывы
                </Link>

                <Link
                    :href="route('settings.index')"
                    class="flex items-center px-4 py-2 rounded-lg transition-colors"
                    :class="route().current('settings.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                >
                    <span class="mr-3">⚙️</span> Настройка
                </Link>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center px-4 py-2 mt-10 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                >
                    <span class="mr-3">🚪</span> Выйти
                </Link>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-8 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>
