<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface ApiKey {
    id: number;
    name: string;
    created_at: string;
    last_used_at: string | null;
    key?: string;
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
const revealedKeys = ref<Record<number, string>>({});

const page = usePage();
const flashApiKey = computed(() => page.props.flash?.api_key);

if (flashApiKey.value) {
    showGeneratedKey.value = true;
    generatedKey.value = flashApiKey.value;
}

const generateApiKey = () => {
    router.post(
        '/api-keys',
        { name: keyName.value || 'API Key' },
        {
            preserveScroll: true,
            onSuccess: () => {
                keyName.value = '';
            },
        },
    );
};

const deleteApiKey = (id: number) => {
    if (
        confirm(
            'Are you sure you want to delete this API key? This action cannot be undone.',
        )
    ) {
        router.delete(`/api-keys/${id}`, {
            preserveScroll: true,
        });
    }
};

const revealApiKey = async (id: number) => {
    try {
        const response = await fetch(`/api-keys/${id}/reveal-json`);
        const data = await response.json();
        if (data.key) {
            revealedKeys.value[id] = data.key;
        }
    } catch (error) {
        console.error('Failed to reveal API key:', error);
    }
};

const hideApiKey = (id: number) => {
    delete revealedKeys.value[id];
};

const copyToClipboard = async (text: string) => {
    try {
        await navigator.clipboard.writeText(text);
        console.log('Copied to clipboard');
    } catch (error) {
        console.error('Failed to copy:', error);
    }
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
            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">API Key Management</h2>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('api-tester')"
                        class="rounded bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
                    >
                        API Tester
                    </Link>
                </div>

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
                            class="rounded bg-blue-500 px-6 py-2 font-semibold text-white hover:bg-blue-600"
                        >
                            Generate New Key
                        </button>
                    </div>
                </div>

                <!-- API Keys List -->
                <div class="space-y-3">
                    <h3 class="mb-3 text-lg font-semibold">Your API Keys</h3>
                    <div
                        v-if="apiKeys.length === 0"
                        class="text-gray-500 dark:text-gray-400"
                    >
                        No API keys generated yet.
                    </div>
                    <div
                        v-for="key in apiKeys"
                        :key="key.id"
                        class="flex flex-col gap-3 rounded border border-gray-200 p-4 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ key.name }}</div>
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Created:
                                    {{
                                        new Date(
                                            key.created_at,
                                        ).toLocaleDateString()
                                    }}
                                    <span v-if="key.last_used_at">
                                        | Last used:
                                        {{
                                            new Date(
                                                key.last_used_at,
                                            ).toLocaleDateString()
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    v-if="!revealedKeys[key.id]"
                                    @click="revealApiKey(key.id)"
                                    class="rounded bg-yellow-500 px-4 py-2 font-semibold text-white hover:bg-yellow-600"
                                >
                                    Reveal
                                </button>
                                <button
                                    v-else
                                    @click="hideApiKey(key.id)"
                                    class="rounded bg-gray-500 px-4 py-2 font-semibold text-white hover:bg-gray-600"
                                >
                                    Hide
                                </button>
                                <button
                                    @click="deleteApiKey(key.id)"
                                    class="rounded bg-red-500 px-4 py-2 font-semibold text-white hover:bg-red-600"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div
                            v-if="revealedKeys[key.id]"
                            class="flex gap-2"
                        >
                            <input
                                :value="revealedKeys[key.id]"
                                readonly
                                type="text"
                                class="flex-1 rounded border border-gray-300 px-3 py-2 font-mono text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                            <button
                                @click="copyToClipboard(revealedKeys[key.id])"
                                class="rounded bg-green-500 px-4 py-2 font-semibold text-white hover:bg-green-600"
                            >
                                Copy
                            </button>
                        </div>
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
            class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
            @click="closeKeyModal"
        >
            <div
                class="mx-4 w-full max-w-2xl rounded-lg bg-white p-6 dark:bg-gray-800"
                @click.stop
            >
                <h3 class="mb-4 text-xl font-bold">API Key Generated</h3>
                <p class="mb-4 text-red-600 dark:text-red-400">
                    ⚠️ Important: Copy this key now. You won't be able to see it
                    again!
                </p>
                <div class="mb-4 flex gap-2">
                    <input
                        :value="generatedKey"
                        readonly
                        class="flex-1 rounded border border-gray-300 px-4 py-2 font-mono text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <button
                        @click="copyToClipboard(generatedKey)"
                        class="rounded bg-green-500 px-4 py-2 font-semibold text-white hover:bg-green-600"
                    >
                        Copy
                    </button>
                </div>
                <button
                    @click="closeKeyModal"
                    class="w-full rounded bg-gray-500 px-4 py-2 font-semibold text-white hover:bg-gray-600"
                >
                    Close
                </button>
            </div>
        </div>
    </AppLayout>
</template>
