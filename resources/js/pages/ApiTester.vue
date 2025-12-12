<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface ApiKey {
    id: number;
    key: string;
    name: string;
}

interface Props {
    apiKeys: ApiKey[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'API Tester',
        href: '/api-tester',
    },
];

const selectedApiKey = ref<number | string>('');
const actualApiKey = ref('');
const revealedKeys = ref<Record<number, string>>({});
const endpoint = ref('/api/playlists');
const method = ref('GET');
const response = ref('');
const loading = ref(false);
const error = ref('');

const availableEndpoints = [
    {
        value: '/api/playlists',
        label: 'GET /api/playlists - Get all your playlists with tracks',
    },
];

const testEndpoint = async () => {
    if (!selectedApiKey.value) {
        error.value = 'Please select an API key';
        return;
    }

    // If selectedApiKey is an ID (number or numeric string), fetch the actual key
    let apiKeyToUse = '';
    const id = Number(selectedApiKey.value);
    if (!Number.isNaN(id) && id > 0) {
        // try cached
        if (revealedKeys.value[id]) {
            apiKeyToUse = revealedKeys.value[id];
        } else {
            try {
                const resKey = await fetch(`/api-keys/${id}/reveal-json`, {
                    method: 'GET',
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!resKey.ok) {
                    throw new Error('Unable to reveal API key');
                }
                const json = await resKey.json();
                revealedKeys.value[id] = json.key;
                apiKeyToUse = json.key;
            } catch (e: any) {
                error.value = e.message || 'Unable to reveal API key';
                return;
            }
        }
    } else {
        apiKeyToUse = String(selectedApiKey.value);
    }

    loading.value = true;
    error.value = '';
    response.value = '';

    try {
        const res = await fetch(endpoint.value, {
            method: method.value,
            headers: {
                'X-API-Key': apiKeyToUse,
                Accept: 'application/json',
            },
        });

        const data = await res.json();
        response.value = JSON.stringify(data, null, 2);
    } catch (err: any) {
        error.value = err.message || 'An error occurred';
    } finally {
        loading.value = false;
    }
};

const hideRevealedKey = () => {
    const id = Number(selectedApiKey.value);
    if (id > 0) {
        delete revealedKeys.value[id];
    }
    actualApiKey.value = '';
};

const copyResponse = async () => {
    try {
        await navigator.clipboard.writeText(response.value);
        console.log('Response copied to clipboard');
    } catch (error) {
        console.error('Failed to copy response:', error);
    }
};

const copyApiKey = async () => {
    try {
        await navigator.clipboard.writeText(actualApiKey.value);
        console.log('API key copied to clipboard');
    } catch (error) {
        console.error('Failed to copy API key:', error);
    }
};

const revealSelected = async () => {
    const id = Number(selectedApiKey.value);
    if (Number.isNaN(id) || id <= 0) {
        error.value = 'Please select an API key to reveal';
        return;
    }
    try {
        const resKey = await fetch(`/api-keys/${id}/reveal-json`, {
            method: 'GET',
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!resKey.ok) throw new Error('Unable to reveal key');
        const json = await resKey.json();
        revealedKeys.value[id] = json.key;
        actualApiKey.value = json.key;
        // set selected to id (keeps selection)
        selectedApiKey.value = id;
        error.value = ''; // Clear any errors on success
    } catch (e: any) {
        error.value = e.message || 'Unable to reveal API key';
    }
};
</script>

<template>
    <Head title="API Tester" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800"
            >
                <h2 class="mb-4 text-2xl font-bold">API Endpoint Tester</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Test your API endpoints with a visual interface. No coding
                    required!
                </p>

                <!-- API Key Selection -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium"
                        >Select API Key</label
                    >
                    <div class="flex gap-2">
                        <select
                            v-model="selectedApiKey"
                            class="flex-1 rounded border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">-- Select an API Key --</option>
                            <option
                                v-for="key in apiKeys"
                                :key="key.id"
                                :value="key.id"
                            >
                                {{ key.name }}
                            </option>
                        </select>
                        <button
                            v-if="
                                selectedApiKey &&
                                !revealedKeys[Number(selectedApiKey)]
                            "
                            @click="revealSelected"
                            class="rounded bg-yellow-500 px-4 py-2 font-semibold text-white hover:bg-yellow-600"
                        >
                            Reveal
                        </button>
                        <button
                            v-else-if="
                                selectedApiKey &&
                                revealedKeys[Number(selectedApiKey)]
                            "
                            @click="hideRevealedKey"
                            class="rounded bg-gray-500 px-4 py-2 font-semibold text-white hover:bg-gray-600"
                        >
                            Hide
                        </button>
                    </div>
                    <div
                        v-if="
                            selectedApiKey &&
                            revealedKeys[Number(selectedApiKey)]
                        "
                        class="mt-2 flex gap-2"
                    >
                        <input
                            :value="revealedKeys[Number(selectedApiKey)]"
                            readonly
                            type="text"
                            class="flex-1 rounded border border-gray-300 px-3 py-2 font-mono text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <button
                            @click="copyApiKey"
                            class="rounded bg-green-500 px-4 py-2 font-semibold text-white hover:bg-green-600"
                        >
                            Copy
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Don't have an API key?
                        <a
                            href="/dashboard"
                            class="text-blue-500 hover:underline"
                            >Generate one here</a
                        >
                    </p>
                </div>

                <!-- Endpoint Selection -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium"
                        >Endpoint</label
                    >
                    <select
                        v-model="endpoint"
                        class="w-full rounded border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option
                            v-for="ep in availableEndpoints"
                            :key="ep.value"
                            :value="ep.value"
                        >
                            {{ ep.label }}
                        </option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Or enter custom endpoint:
                    </p>
                    <input
                        v-model="endpoint"
                        type="text"
                        placeholder="/api/playlists"
                        class="mt-2 w-full rounded border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <!-- Test Button -->
                <button
                    @click="testEndpoint"
                    :disabled="loading"
                    class="mb-4 w-full rounded bg-blue-500 px-6 py-3 font-semibold text-white hover:bg-blue-600 disabled:bg-gray-400"
                >
                    {{ loading ? 'Loading...' : 'Test Endpoint' }}
                </button>

                <!-- Error Message -->
                <div
                    v-if="error"
                    class="mb-4 rounded border border-red-400 bg-red-100 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400"
                >
                    <strong>Error:</strong> {{ error }}
                </div>

                <!-- Response -->
                <div v-if="response" class="mb-4">
                    <div class="mb-2 flex items-center justify-between">
                        <label class="block text-sm font-medium"
                            >Response</label
                        >
                        <button
                            @click="copyResponse"
                            class="rounded bg-green-500 px-3 py-1 text-sm font-semibold text-white hover:bg-green-600"
                        >
                            Copy
                        </button>
                    </div>
                    <pre
                        class="overflow-x-auto rounded bg-gray-900 p-4 text-sm text-green-400"
                        >{{ response }}</pre
                    >
                </div>

                <!-- Instructions -->
                <div
                    class="mt-6 rounded border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20"
                >
                    <h3 class="mb-2 font-semibold">How to use:</h3>
                    <ol
                        class="list-inside list-decimal space-y-1 text-sm text-gray-700 dark:text-gray-300"
                    >
                        <li>
                            Select an API key from the dropdown (or generate one
                            from the dashboard)
                        </li>
                        <li>
                            Choose an endpoint to test or enter a custom one
                        </li>
                        <li>Click "Test Endpoint" to see the response</li>
                        <li>
                            Copy the response if needed for your application
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
