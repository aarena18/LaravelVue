<template>
    <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white dark:bg-gray-800">
        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700">
            <img 
                v-if="playlist.first_track_image" 
                class="w-full h-full object-cover" 
                :src="`/storage/${playlist.first_track_image}`"
                :alt="playlist.title"
            >
            <div 
                v-else 
                class="w-full h-full flex items-center justify-center text-gray-400"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2">{{ playlist.title }}</div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                {{ playlist.tracks_count }} {{ playlist.tracks_count === 1 ? 'musique' : 'musiques' }}
            </p>
        </div>
        <div class="px-6 pb-4 flex gap-2">
            <Link
                :href="route('playlists.edit', { playlist: playlist })"
                class="flex-1 bg-transparent hover:bg-green-500 text-green-700 dark:text-green-400 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded text-center"
            >
                Modifier
            </Link>
            <button
                @click="destroy"
                class="flex-1 bg-transparent hover:bg-red-500 text-red-700 dark:text-red-400 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded text-center cursor-pointer"
            >
                Supprimer
            </button>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        name: 'PlaylistCard',
        components: {
            Link,
        },
        props: {
            playlist: {
                type: Object,
                required: true,
            },
        },
        methods: {
            destroy() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette playlist ?')) {
                    this.$inertia.delete(route('playlists.destroy', { playlist: this.playlist }), {
                        preserveScroll: true,
                    });
                }
            },
        },
    };
</script>
