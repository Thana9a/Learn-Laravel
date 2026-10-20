<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { navigation, type NavigationGroup, type NavigationItem } from '@/config/navigation';
import { usePermission } from '@/Composables/usePermission';
import en from '@/lang/en';
import { Coffee } from 'lucide-vue-next';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
  useSidebar as useUiSidebar,
} from '@/Components/ui/sidebar';

interface AuthUser {
  name?: string;
  email?: string;
}

const page = usePage();
const t = en.menu;
const { can } = usePermission();
const { setOpenMobile, state } = useUiSidebar();

const railLabel = computed<string>(() =>
  state.value === 'expanded'
    ? en.common.nav.collapse_sidebar
    : en.common.nav.expand_sidebar,
);

const authUser = computed<AuthUser | null>(() => {
  const pageProps = page.props as { auth?: { user?: AuthUser } };

  return pageProps.auth?.user ?? null;
});

const userInitial = computed<string>(() =>
  authUser.value?.name ? authUser.value.name.charAt(0).toUpperCase() : 'U',
);

const visibleGroups = computed<NavigationGroup[]>(() =>
  navigation
    .map((group) => ({
      ...group,
      items: group.items.filter((item) => canAccess(item)),
    }))
    .filter((group) => group.items.length > 0),
);

function canAccess(item: NavigationItem): boolean {
  return !item.permission || can(item.permission);
}

/**
 * Matches the item's route and its feature routes, so `orders.index` stays
 * active while viewing `orders.show` (and likewise for edit pages).
 */
function isActive(item: NavigationItem): boolean {
  try {
    if (route().current(item.route)) {
      return true;
    }

    const base = item.route.replace(/\.(index|edit)$/, '');

    return base !== item.route && route().current(`${base}.*`);
  } catch {
    return false;
  }
}

/** On mobile the sidebar is a sheet, so close it after choosing a link. */
function closeMobileSidebar(): void {
  setOpenMobile(false);
}
</script>

<template>
  <Sidebar collapsible="icon">
    <SidebarHeader>
      <div
        class="flex items-center gap-3 px-2 py-1.5 group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:px-0"
      >
        <div
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground shadow-sm group-data-[collapsible=icon]:h-8 group-data-[collapsible=icon]:w-8"
        >
          <Coffee class="h-5 w-5" />
        </div>

        <div class="min-w-0 group-data-[collapsible=icon]:hidden">
          <h1 class="truncate text-sm font-semibold leading-tight text-sidebar-foreground">
            Coffee POS
          </h1>
          <p class="truncate text-xs text-sidebar-foreground/70">Staff Dashboard</p>
        </div>
      </div>
    </SidebarHeader>

    <SidebarContent>
      <SidebarGroup v-for="group in visibleGroups" :key="group.groupKey">
        <SidebarGroupLabel>
          {{ t.groups[group.labelKey] }}
        </SidebarGroupLabel>

        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.route">
              <SidebarMenuButton
                as-child
                :is-active="isActive(item)"
                :tooltip="t.items[item.labelKey]"
              >
                <Link
                  :href="route(item.route)"
                  :aria-current="isActive(item) ? 'page' : undefined"
                  @click="closeMobileSidebar"
                >
                  <component :is="item.icon" />
                  <span>{{ t.items[item.labelKey] }}</span>
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter>
      <SidebarMenu v-if="authUser">
        <SidebarMenuItem>
          <SidebarMenuButton
            as-child
            size="lg"
            :tooltip="authUser.name || t.items.profile"
          >
            <Link :href="route('profile.edit')" @click="closeMobileSidebar">
              <span
                class="flex aspect-square size-8 shrink-0 items-center justify-center rounded-lg bg-sidebar-primary text-sm font-semibold text-sidebar-primary-foreground"
              >
                {{ userInitial }}
              </span>

              <span class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">{{ authUser.name }}</span>
                <span class="truncate text-xs text-sidebar-foreground/70">
                  {{ authUser.email }}
                </span>
              </span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarFooter>

    <SidebarRail :label="railLabel" />
  </Sidebar>
</template>
