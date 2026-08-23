<script setup lang="ts">
import { computed, type Component } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { menu, type MenuItem } from '@/config/menu';
import { usePermission } from '@/Composables/usePermission';
import en from '@/lang/en';
import {
  LayoutDashboard,
  Coffee,
  ShoppingBag,
  Receipt,
  BarChart3,
  User,
  Users,
  Settings,
  Percent,
  CreditCard,
} from 'lucide-vue-next';

interface AuthUser {
  name?: string;
  email?: string;
  permissions?: string[];
}

const iconComponents: Record<string, Component> = {
  LayoutDashboard,
  Coffee,
  ShoppingBag,
  Receipt,
  BarChart3,
  User,
  Users,
  Settings,
  Percent,
  CreditCard,
};

const page = usePage();
const t = en;
const { can } = usePermission();

const authUser = computed<AuthUser | null>(() => {
  const props = page.props as { auth?: { user?: AuthUser } };

  return props.auth?.user ?? null;
});

function canAccess(item: MenuItem): boolean {
  if (!item.permission) {
    return true;
  }
  return can(item.permission);
}

function isActiveRoute(routeName: string): boolean {
  try {
    return route().current(routeName) || route().current(`${routeName}.*`);
  } catch {
    return false;
  }
}

function labelKeyName(key: string): string {
  return key.split('.').pop() ?? key;
}

function groupLabel(key: string): string {
  const label = labelKeyName(key) as keyof typeof t.menu.groups;

  return t.menu.groups[label] ?? key;
}

function itemLabel(item: MenuItem): string {
  const label = labelKeyName(item.labelKey) as keyof typeof t.menu.items;

  return t.menu.items[label] ?? item.name;
}
</script>

<template>
  <aside class="w-64 bg-white border-r border-neutral-200 flex flex-col h-screen select-none dark:bg-neutral-900 dark:border-neutral-700">
    <div class="h-16 flex items-center px-6 border-b border-neutral-200 gap-3 dark:border-neutral-700">
      <div class="w-9 h-9 rounded-lg bg-primary-500 flex items-center justify-center text-white shadow-sm font-bold text-lg">
        <Coffee class="h-5 w-5" />
      </div>
      <div>
        <h1 class="font-semibold text-neutral-900 leading-tight dark:text-neutral-100">Coffee POS</h1>
        <p class="text-xs text-neutral-500 dark:text-neutral-400">Staff Dashboard</p>
      </div>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-6">
      <div v-for="group in menu" :key="group.groupKey" class="space-y-1">
        <h2 class="px-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider mb-2">
          {{ groupLabel(group.labelKey) }}
        </h2>

        <div class="space-y-0.5">
          <template v-for="item in group.items" :key="item.route">
            <Link
              v-if="canAccess(item)"
              :href="route(item.route)"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
              :class="[
                isActiveRoute(item.route)
                  ? 'bg-primary-50 text-primary-600 font-semibold dark:bg-primary-500/10 dark:text-primary-500'
                  : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100',
              ]"
            >
              <component
                :is="iconComponents[item.icon] || Coffee"
                class="w-5 h-5 flex-shrink-0"
                :class="[isActiveRoute(item.route) ? 'text-primary-500' : 'text-neutral-400']"
              />
              <span class="truncate">{{ itemLabel(item) }}</span>
            </Link>
          </template>
        </div>
      </div>
    </nav>

    <div v-if="authUser" class="p-4 border-t border-neutral-200 bg-neutral-50/50 dark:border-neutral-700 dark:bg-neutral-800/50">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-neutral-200 text-neutral-700 flex items-center justify-center font-semibold text-sm dark:bg-neutral-700 dark:text-neutral-200">
          {{ authUser.name ? authUser.name.charAt(0).toUpperCase() : 'U' }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-neutral-900 truncate dark:text-neutral-100">{{ authUser.name }}</p>
          <p class="text-xs text-neutral-500 truncate dark:text-neutral-400">{{ authUser.email }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>
