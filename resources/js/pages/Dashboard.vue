<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface ApiKey {
    id: number;
    name: string;
    created_at: string;
    last_used_at: string | null;
}

interface Props {
    apiKeys: ApiKey[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const keyName = ref('');
const showGeneratedKey = ref(false);
const generatedKey = ref('');

const page = usePage();
const flashApiKey = computed(() => page.props.flash?.api_key);

if (flashApiKey.value) {
    showGeneratedKey.value = true;
    generatedKey.value = flashApiKey.value;
}

const generateApiKey = () => {
    router.post('/api-keys', { name: keyName.value || 'API Key' }, {
        preserveScroll: true,
        onSuccess: () => {
            keyName.value = '';
        },
    });
};

const deleteApiKey = (id: number) => {
    if (confirm('Are you sure you want to delete this API key? This action cannot be undone.')) {
        router.delete(`/api-keys/${id}`, {
            preserveScroll: true,
        });
    }
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
};

const closeKeyModal = () => {
    showGeneratedKey.value = false;
    generatedKey.value = '';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- API Key Management Section -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800">
                <h2 class="text-2xl font-bold mb-4">API Key Management</h2>
                
                <!-- Generate API Key Form -->
                <div class="mb-6">
                    <div class="flex gap-2">
                        <input
                            v-model="keyName"
                            type="text"
                            placeholder="API Key Name (optional)"
                            class="flex-1 rounded border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <button
                            @click="generateApiKey"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded"
                        >
                            Generate New Key
                        </button>
                    </div>
                </div>

                <!-- API Keys List -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold mb-3">Your API Keys</h3>
                    <div v-if="apiKeys.length === 0" class="text-gray-500 dark:text-gray-400">
                        No API keys generated yet.
                    </div>
                    <div
                        v-for="key in apiKeys"
                        :key="key.id"
                        class="flex items-center justify-between p-4 border border-gray-200 rounded dark:border-gray-700"
                    >
                        <div>
                            <div class="font-semibold">{{ key.name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Created: {{ new Date(key.created_at).toLocaleDateString() }}
                                <span v-if="key.last_used_at">
                                    | Last used: {{ new Date(key.last_used_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                        <button
                            @click="deleteApiKey(key.id)"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
        </div>

        <!-- Modal for displaying generated API key -->
        <div
            v-if="showGeneratedKey"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            @click="closeKeyModal"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4"
                @click.stop
            >
                <h3 class="text-xl font-bold mb-4">API Key Generated</h3>
                <p class="text-red-600 dark:text-red-400 mb-4">
                    ⚠️ Important: Copy this key now. You won't be able to see it again!
                </p>
                <div class="flex gap-2 mb-4">
                    <input
                        :value="generatedKey"
                        readonly
                        class="flex-1 rounded border border-gray-300 px-4 py-2 font-mono text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <button
                        @click="copyToClipboard(generatedKey)"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                    >
                        Copy
                    </button>
                </div>
                <button
                    @click="closeKeyModal"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded w-full"
                >
                    Close
                </button>
            </div>
        </div>
    </AppLayout>
</template>
