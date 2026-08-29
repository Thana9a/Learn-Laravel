<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Sidebar from '@/Components/Sidebar.vue';
import en from '@/lang/en';
import { Coffee, LogOut, Menu, Moon, Sun, User, X } from 'lucide-vue-next';
import { useDarkMode } from '@/Composables/useDarkMode';

interface AuthUser {
    name?: string;
    email?: string;
}

const page = usePage();
const showingMobileSidebar = ref(false);
const t = en.common;
const { isDark, toggle: toggleDarkMode } = useDarkMode();

const authUser = computed<AuthUser | null>(() => {
    const props = page.props as { auth?: { user?: AuthUser } };

    return props.auth?.user ?? null;
});
</script>

<template>
    <div class="min-h-screen bg-surface-muted text-foreground">
        <div class="lg:flex">
            <Sidebar class="hidden lg:flex lg:fixed lg:inset-y-0 lg:left-0" />

            <Transition name="fade">
                <div
                    v-if="showingMobileSidebar"
                    class="fixed inset-0 z-40 bg-neutral-900/50 backdrop-blur-sm lg:hidden"
                    @click="showingMobileSidebar = false"
                />
            </Transition>

            <Transition name="slide">
                <div
                    v-if="showingMobileSidebar"
                    class="fixed inset-y-0 left-0 z-50 lg:hidden"
                >
                    <Sidebar />
                </div>
            </Transition>

            <div class="min-h-screen flex-1 lg:pl-64">
                <header class="sticky top-0 z-30 border-b border-border bg-white/95 backdrop-blur dark:bg-neutral-900/95">
                    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-900 lg:hidden"
                                aria-label="Open navigation"
                                @click="showingMobileSidebar = true"
                            >
                                <Menu class="h-5 w-5" />
                            </button>

                            <Link :href="route('dashboard')" class="flex items-center gap-3 lg:hidden">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-500 text-white shadow-sm">
                                    <Coffee class="h-5 w-5" />
                                </span>
                                <span class="text-sm font-semibold text-neutral-900">Coffee POS</span>
                            </Link>

                            <slot name="header" />
                        </div>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('pos.index')"
                                class="hidden items-center gap-2 rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700 sm:inline-flex"
                            >
                                <Coffee class="h-4 w-4" />
                                {{ t.nav.pos }}
                            </Link>

                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted transition-colors hover:bg-neutral-100 hover:text-neutral-900 dark:hover:bg-neutral-700 dark:hover:text-neutral-100"
                                aria-label="Toggle dark mode"
                                @click="toggleDarkMode"
                            >
                                <Sun v-if="isDark" class="h-5 w-5" />
                                <Moon v-else class="h-5 w-5" />
                            </button>

                            <Dropdown v-if="authUser" align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-surface px-3 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-100 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                                    >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-700">
                                            {{ authUser.name ? authUser.name.charAt(0).toUpperCase() : 'U' }}
                                        </span>
                                        <span class="hidden max-w-32 truncate sm:inline">{{ authUser.name }}</span>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        <span class="inline-flex items-center gap-2">
                                            <User class="h-4 w-4" />
                                            {{ t.nav.profile }}
                                        </span>
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        <span class="inline-flex items-center gap-2">
                                            <LogOut class="h-4 w-4" />
                                            {{ t.nav.logout }}
                                        </span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </header>

                <main>
                    <slot />
                </main>
            </div>
        </div>

        <button
            v-if="showingMobileSidebar"
            type="button"
            class="fixed right-4 top-4 z-50 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-white text-neutral-700 shadow-sm ring-1 ring-neutral-200 lg:hidden"
            aria-label="Close navigation"
            @click="showingMobileSidebar = false"
        >
            <X class="h-5 w-5" />
        </button>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active,
.slide-enter-active,
.slide-leave-active {
    transition: all 0.18s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}
</style>
