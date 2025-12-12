<template>
    <div>
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex flex-shrink-0 items-center">
                    <img class="h-8 w-auto" src="/images/spotify.png">
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                    <div class="flex space-x-4">
                        <Link
                            :href="route('tracks.index')"
                            :class="[route().current().includes('tracks') ? 'text-white-500' : 'text-gray-400']"
                        >
                            Musiques
                        </Link>
                        <Link
                            :href="route('playlists.index')"
                            :class="[route().current().includes('playlists') ? 'text-white-500' : 'text-gray-400']"
                        >
                            Playlists
                        </Link>
                    </div>
                    </div>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('logout')"
                        method="post"
                        as="button"
                        preserve-scroll
                    >
                        Log out
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
                </div>
            </div>
        </nav>

        <div class="px-16">
            <div class="flex justify-between items-center">
                <h4 class="text-3xl font-bold my-6">
                    <slot name="title" />
                </h4>
                <div>
                    <slot name="actions" />
                </div>
            </div>

            <slot name="content" />
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        name: 'MusicLayout',
        components: {
            Link,
        },
    }
</script>
