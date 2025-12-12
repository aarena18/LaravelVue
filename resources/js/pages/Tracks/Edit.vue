<template>
    <MusicLayout>
        <template #title>
            Modifier la musique {{ track.title }}
        </template>

        <template #actions>
            <Link :href="route('tracks.index')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Retour</Link>
        </template>

        <template #content>
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
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="artist">
                        Artiste
                    </label>
                    <input
                        id="artist"
                        v-model="form.artist"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        :class="{ 'border-red-500': form.errors.artist }"
                        type="text"
                        placeholder="Artiste"
                    >
                    <small class="text-red-500">{{ form.errors.artist }}</small>
                </div>

                <input
                    type="submit"
                    value="Modifier la musique"
                    :disabled="form.processing"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
            </form>
        </template>
    </MusicLayout>
</template>

<script>
    import MusicLayout from '@/layouts/MusicLayout.vue';
    import { Link } from '@inertiajs/vue3';

    export default {
        name: 'Edit',
        components: {
            Link,
            MusicLayout,
        },
        props: {
            track: Object,
        },
        data() {
            return {
                form: this.$inertia.form({
                    title: this.track.title,
                    artist: this.track.artist,
                }),
            }
        },
        methods: {
            submit() {
                this.form.put(route('tracks.update', { track: this.track }));
            }
        }
    }
</script>
