<template>
    <form @submit.prevent="submit">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Titre
            </label>
            <input
                id="title"
                v-model="form.title"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{ 'border-red-500': form.errors.title }"
                type="text"
                placeholder="Title"
            >
            <small class="text-red-500">{{ form.errors.title }}</small>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Musiques
            </label>

            <div
                v-for="track in tracks"
                :key="track.slug"
            >
                <input
                    :id="'tracks_' + track.slug"
                    v-model="form.tracks"
                    type="checkbox"
                    name="tracks"
                    :value="track.slug"
                    class="mr-3"
                >
                <label :for="'tracks_' + track.slug">{{ track.title }}</label>
            </div>
        </div>

        <input
            type="submit"
            :value="playlist ? 'Modifier la playlist' : 'Créer la playlist'"
            :disabled="form.processing"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
    </form>
</template>

<script>
    export default {
        name: 'PlaylistForm',
        props: {
            playlist: Object,
            tracks: Array,
        },
        data() {
            return {
                form: this.$inertia.form({
                    title: this.playlist?.title ?? '',
                    tracks: this.playlist?.tracks.map(track => track.slug) ?? [],
                }),
            }
        },
        methods: {
            submit() {
                if (this.playlist) {
                    this.form.put(route('playlists.update', { playlist: this.playlist }));
                } else {
                    this.form.post(route('playlists.store'));
                }
            }
        }
    }
</script>
