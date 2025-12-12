<template>
    <MusicLayout>
        <template #title>
            Créer une musique
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

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="artist">
                        Image
                    </label>
                    <input type="file" name="image" id="image" @input="form.image = $event.target.files[0]">
                    <small class="text-red-500">{{ form.errors.image }}</small>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="music">
                        Audio
                    </label>
                    <input type="file" name="music" id="music" @input="form.music = $event.target.files[0]">
                    <small class="text-red-500">{{ form.errors.music }}</small>
                </div>

                <input
                    type="submit"
                    value="Créer la musique"
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
        name: 'Create',
        components: {
            Link,
            MusicLayout,
        },
        data() {
            return {
                form: this.$inertia.form({
                    title: '',
                    artist: '',
                    image: null,
                    music: null,
                }),
            }
        },
        methods: {
            submit() {
                // this.$inertia.post(route('tracks.store'), this.form);

                this.form.post(route('tracks.store'));
            }
        }
    }
</script>
