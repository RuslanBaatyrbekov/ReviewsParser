<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ reviews: Object, stats: Object, filters: Object });

const sort = ref(props.filters.sort || 'newest');
watch(sort, (val) => router.get(route('reviews.index'), { sort: val }, { preserveState: true }));
</script>

<template>
    <Head title="Отзывы" />
    <AuthenticatedLayout>

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Отзывы</h2>

        <div v-if="reviews.data.length > 0" class="space-y-4">

            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 flex justify-between items-center">
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ stats.average_rating }}</div>
                    <div class="text-yellow-400 text-lg">★★★★★</div>
                    <div class="text-gray-500 text-sm">Всего отзывов: {{ stats.total_reviews }}</div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Сортировка:</span>
                    <select v-model="sort" class="border-gray-300 rounded text-sm focus:ring-blue-500">
                        <option value="newest">Сначала новые</option>
                        <option value="oldest">Сначала старые</option>
                    </select>
                </div>
            </div>

            <div v-for="review in reviews.data" :key="review.id" class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold overflow-hidden">
                            <img v-if="review.avatar_url" :src="review.avatar_url" class="w-full h-full object-cover">
                            <span v-else>{{ review.author_name[0] }}</span>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ review.author_name }}</div>
                            <div class="text-xs text-gray-400">{{ new Date(review.published_at).toLocaleDateString() }}</div>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 text-sm">
                        <span v-for="n in 5" :key="n">{{ n <= review.rating ? '★' : '☆' }}</span>
                    </div>
                </div>
                <p class="text-gray-700 mt-2">{{ review.text }}</p>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <component
                    :is="link.url ? Link : 'span'"
                    v-for="link in reviews.links" :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1 border rounded text-sm"
                    :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                />
            </div>
        </div>

        <div v-else class="text-center py-20">
            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 text-4xl">
                📭
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Отзывов пока нет</h3>
            <p class="text-gray-500 mb-6">Подключите Яндекс Карты, чтобы начать собирать отзывы.</p>

            <Link
                :href="route('settings.index')"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
            >
                Перейти к настройке
            </Link>
        </div>

    </AuthenticatedLayout>
</template>
