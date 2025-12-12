<template>
    <MusicLayout>
        <template #title>
            Modifier une playlist
        </template>

        <template #actions>
            <Link :href="route('playlists.index')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Retour</Link>
        </template>

        <template #content>
            <PlaylistForm :playlist="playlist" :tracks="tracks" />
        </template>
    </MusicLayout>
</template>

<script>
    import MusicLayout from '@/layouts/MusicLayout.vue';
    import { Link } from '@inertiajs/vue3';
    import PlaylistForm from '@/components/Playlists/PlaylistForm.vue';

    export default {
        name: 'Create',
        components: {
            Link,
            MusicLayout,
            PlaylistForm,
        },
        props: {
            playlist: Object,
            tracks: Array,
        },
    }
</script>
