<template>
    <MusicLayout>
        <template #title>
            Liste des playlists
        </template>

        <template #actions>
            <Link
                :href="route('playlists.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Créer une playlist
            </Link>
        </template>

        <template #content>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <PlaylistCard
                    v-for="playlist in playlists"
                    :key="playlist.slug"
                    :playlist="playlist"
                />
            </div>
        </template>
    </MusicLayout>
</template>

<script>
    import MusicLayout from '@/layouts/MusicLayout.vue';
    import PlaylistCard from '@/components/Playlists/PlaylistCard.vue';
    import { Link } from '@inertiajs/vue3';

    export default {
        name: 'Index',
        components: {
            Link,
            MusicLayout,
            PlaylistCard,
        },
        props: {
            playlists: Array,
        },
    }
</script>
