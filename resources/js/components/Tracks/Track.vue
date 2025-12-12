<template>
    <div class="max-w-sm rounded overflow-hidden shadow-lg flex flex-col h-full">
        <div
            class="w-full h-64 cursor-pointer flex-shrink-0"
            @click="handleClick"
        >
            <img class="w-full h-full object-cover" :src="`/storage/${track.image}`">
        </div>
        <div class="px-6 py-4 cursor-pointer" @click="handleClick">
            <div class="font-bold text-xl mb-2">{{ track.title }}</div>
            <small class="text-gray-700 text-base">
                {{ track.artist }}
            </small>
        </div>
        <div class="px-6 pb-4">
            <!-- Playlist Management for Logged-in Users -->
            <div v-if="$page.props.auth.user && userPlaylists.length > 0" class="mb-3">
                <div class="relative">
                    <button
                        @click="toggleDropdown"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded flex items-center justify-between"
                    >
                        <span>{{ showDropdown ? 'Fermer' : 'Ajouter à une playlist' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div
                        v-if="showDropdown"
                        class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-lg max-h-48 overflow-y-auto"
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
                                class="bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-2 rounded"
                            >
                                Retirer
                            </button>
                            <button
                                v-else
                                @click="addToPlaylist(playlist)"
                                class="bg-green-500 hover:bg-green-600 text-white text-xs py-1 px-2 rounded"
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
                    class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded text-center"
                >
                    Modifier
                </Link>
                <span
                    @click="destroy"
                    class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded text-center cursor-pointer"
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
                this.$inertia.delete(route('tracks.destroy', { track: this.track }), {
                    preserveScroll: true,
                });
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
                        track: this.track.slug 
                    }),
                    {},
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.showDropdown = false;
                        },
                    }
                );
            },
            removeFromPlaylist(playlist) {
                this.$inertia.delete(
                    route('playlists.tracks.detach', { 
                        playlist: playlist.slug, 
                        track: this.track.slug 
                    }),
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.showDropdown = false;
                        },
                    }
                );
            },
        },
    }
</script>
