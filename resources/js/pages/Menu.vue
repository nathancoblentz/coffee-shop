<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

type MenuItem = {
    id: number;
    name: string;
    description: string | null;
    price: string;
};

defineProps<{ menuItems: MenuItem[] }>();
</script>

<template>
    <Head title="Menu" />

    <main class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-3xl font-bold">Menu</h1>

        <ul class="grid gap-4 sm:grid-cols-2">
            <li
                v-for="item in menuItems"
                :key="item.id"
                class="rounded-lg border p-4"
            >
                <div class="flex items-baseline justify-between">
                    <h2 class="font-semibold">{{ item.name }}</h2>
                    <span class="tabular-nums">${{ item.price }}</span>
                </div>
                <p v-if="item.description" class="mt-1 text-sm opacity-70">
                    {{ item.description }}
                </p>
            </li>
        </ul>
    </main>
</template>