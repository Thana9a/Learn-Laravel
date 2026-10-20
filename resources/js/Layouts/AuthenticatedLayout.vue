<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import AppSidebar from '@/Components/AppSidebar.vue';
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/Components/ui/sidebar';
import en from '@/lang/en';
import { Coffee, LogOut, Moon, Sun, User } from 'lucide-vue-next';
import { useDarkMode } from '@/Composables/useDarkMode';
import { useSidebar } from '@/Composables/useSidebar';

interface AuthUser {
    name?: string;
    email?: string;
}

const page = usePage();
const t = en.common;
const { isDark, toggle: toggleDarkMode } = useDarkMode();
const { isCollapsed } = useSidebar();

/** Bridge the persisted collapse preference with shadcn's SidebarProvider. */
const sidebarOpen = computed<boolean>({
    get: () => !isCollapsed.value,
    set: (open: boolean) => {
        isCollapsed.value = !open;
    },
});

const authUser = computed<AuthUser | null>(() => {
    const props = page.props as { auth?: { user?: AuthUser } };

    return props.auth?.user ?? null;
});
</script>

<template>
    <SidebarProvider v-model:open="sidebarOpen">
        <AppSidebar />

        <SidebarInset class="bg-surface-muted">
            <header class="sticky top-0 z-30 border-b border-border bg-white/95 backdrop-blur dark:bg-neutral-900/95">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <SidebarTrigger
                            class="-ml-1"
                            :aria-label="isCollapsed ? t.nav.expand_sidebar : t.nav.collapse_sidebar"
                        />

                        <Link :href="route('dashboard')" class="flex items-center gap-3 md:hidden">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-500 text-white shadow-sm">
                                <Coffee class="h-5 w-5" />
                            </span>
                            <span class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Coffee POS</span>
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
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-neutral-100 hover:text-neutral-900 dark:hover:bg-neutral-700 dark:hover:text-neutral-100"
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

            <div class="flex-1">
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
