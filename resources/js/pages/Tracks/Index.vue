<template>
    <MusicLayout>
        <template #title> Liste des musiques </template>

        <template #actions>
            <Link
                v-if="$page.props.auth.user?.admin"
                :href="route('tracks.create')"
                class="rounded bg-blue-500 px-4 py-2 font-bold text-white hover:bg-blue-700"
            >
                Créer une musique
            </Link>
        </template>

        <template #content>
            <input
                id="search"
                v-model="search"
                type="search"
                name="search"
                class="focus:shadow-outline mb-5 appearance-none rounded border px-3 py-2 leading-tight text-gray-700 shadow focus:outline-none"
                placeholder="Rechercher..."
            />

            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Track
                    v-for="track in filteredTracks"
                    :key="track.slug"
                    :track="track"
                    :user-playlists="userPlaylists"
                    :track-playlist-ids="trackPlaylistIds[track.id] || []"
                    @listen="handleListen"
                />
            </div>
        </template>
    </MusicLayout>
</template>

<script>
import Track from '@/components/Tracks/Track.vue';
import MusicLayout from '@/layouts/MusicLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

export default {
    name: 'Index',
    components: {
        Link,
        MusicLayout,
        Track,
    },
    props: {
        tracks: Array,
        userPlaylists: Array,
        trackPlaylistIds: Object,
    },
    data() {
        return {
            audio: null,
            currentAudio: null,
            search: '',
            lastUserId: null,
        };
    },
    computed: {
        filteredTracks() {
            return this.tracks.filter((track) =>
                track.title.toLowerCase().includes(this.search.toLowerCase()),
            );
        },
    },
    mounted() {
        const page = usePage();
        this.lastUserId = page.props.auth.user?.id;
    },
    watch: {
        '$page.props.auth.user.id': function (newId, oldId) {
            // When user ID changes or user logs out/in, reload page
            if (newId !== this.lastUserId) {
                this.lastUserId = newId;
                // Reload without preserving state to get fresh data
                router.get(route('tracks.index'), {}, { preserveState: false });
            }
        },
    },
    methods: {
        changeCurrentAudio() {
            this.audio = new Audio('/storage/' + track.audio);
            this.audio.play();
            this.currentAudio = track.slug;
        },
        handleListen(track) {
            if (!this.audio) {
                this.changeCurrentAudio();
            } else if (track.slug !== this.currentAudio) {
                this.audio.pause();
                this.changeCurrentAudio();
            } else if (this.audio.paused) {
                this.audio.play();
            } else {
                this.audio.pause();
            }
        },
    },
};
</script>
