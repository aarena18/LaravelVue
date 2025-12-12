<template>
    <div
        class="flex h-full max-w-sm flex-col overflow-hidden rounded shadow-lg"
    >
        <div
            class="h-64 w-full flex-shrink-0 cursor-pointer"
            @click="handleClick"
        >
            <img
                class="h-full w-full object-cover"
                :src="`/storage/${track.image}`"
            />
        </div>
        <div class="cursor-pointer px-6 py-4" @click="handleClick">
            <div class="mb-2 text-xl font-bold">{{ track.title }}</div>
            <small class="text-base text-gray-700">
                {{ track.artist }}
            </small>
        </div>
        <div class="px-6 pb-4">
            <!-- Playlist Management for Logged-in Users -->
            <div
                v-if="$page.props.auth.user"
                class="mb-3"
            >
                <div v-if="userPlaylists.length === 0" class="text-sm text-gray-500 dark:text-gray-400 p-2 text-center">
                    Créez une playlist pour ajouter cette musique
                </div>
                <div v-else class="relative">
                    <button
                        @click="toggleDropdown"
                        class="flex w-full items-center justify-between rounded bg-blue-500 px-4 py-2 font-semibold text-white hover:bg-blue-600"
                    >
                        <span>{{
                            showDropdown ? 'Fermer' : 'Ajouter à une playlist'
                        }}</span>
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            ></path>
                        </svg>
                    </button>
                    <div
                        v-if="showDropdown"
                        class="absolute z-10 mt-1 max-h-48 w-full overflow-y-auto rounded border border-gray-300 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"
                    >
                        <div
                            v-for="playlist in userPlaylists"
                            :key="playlist.id"
                            class="flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <span class="text-sm">{{ playlist.title }}</span>
                            <button
                                v-if="isTrackInPlaylist(playlist.id)"
                                @click="removeFromPlaylist(playlist)"
                                class="rounded bg-red-500 px-2 py-1 text-xs text-white hover:bg-red-600"
                            >
                                Retirer
                            </button>
                            <button
                                v-else
                                @click="addToPlaylist(playlist)"
                                class="rounded bg-green-500 px-2 py-1 text-xs text-white hover:bg-green-600"
                            >
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div v-if="$page.props.isAdmin" class="grid grid-cols-2 gap-2">
                <Link
                    :href="route('tracks.edit', { track: track })"
                    class="rounded border border-green-500 bg-transparent px-4 py-2 text-center font-semibold text-green-700 hover:border-transparent hover:bg-green-500 hover:text-white"
                >
                    Modifier
                </Link>
                <span
                    @click="destroy"
                    class="cursor-pointer rounded border border-red-500 bg-transparent px-4 py-2 text-center font-semibold text-red-700 hover:border-transparent hover:bg-red-500 hover:text-white"
                >
                    Supprimer
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: 'Track',
    emits: ['listen'],
    components: {
        Link,
    },
    props: {
        track: Object,
        userPlaylists: {
            type: Array,
            default: () => [],
        },
        trackPlaylistIds: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            showDropdown: false,
        };
    },
    methods: {
        destroy() {
            this.$inertia.delete(
                route('tracks.destroy', { track: this.track }),
                {
                    preserveScroll: true,
                },
            );
        },
        handleClick() {
            this.$emit('listen', this.track);
        },
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },
        isTrackInPlaylist(playlistId) {
            return this.trackPlaylistIds.includes(playlistId);
        },
        addToPlaylist(playlist) {
            this.$inertia.post(
                route('playlists.tracks.attach', {
                    playlist: playlist.slug,
                    track: this.track.slug,
                }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showDropdown = false;
                    },
                },
            );
        },
        removeFromPlaylist(playlist) {
            this.$inertia.delete(
                route('playlists.tracks.detach', {
                    playlist: playlist.slug,
                    track: this.track.slug,
                }),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.showDropdown = false;
                    },
                },
            );
        },
    },
};
</script>
