<script setup lang="ts">
import { computed, type Component } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import { usePermission } from '@/Composables/usePermission';
import { useDashboard, type DashboardStat } from '@/Composables/useDashboard';
import { useOrderFormat } from '@/Composables/useOrderFormat';
import { statusBadgeClass, statusDotClass, statusLabel } from '@/lib/status';
import type { RecentOrder } from '@/Types/order.type';
import {
  ArrowRight,
  BarChart3,
  CalendarDays,
  Clock,
  Coffee,
  Receipt,
  ShoppingBag,
  Users,
} from 'lucide-vue-next';
import en from '@/lang/en';

const t = en.dashboard;
const { can } = usePermission();
const { money, formatTime } = useOrderFormat();

const props = withDefaults(defineProps<{
  stats?: DashboardStat[];
  recentOrders?: RecentOrder[];
}>(), {
  stats: () => [],
  recentOrders: () => [],
});

const { userName, displayStats, resolveIcon, currentDate, currentTime } = useDashboard(props.stats);

type ActionColor = 'primary' | 'info' | 'warning' | 'success';

interface QuickAction {
  key: string;
  href: string;
  icon: Component;
  title: string;
  description: string;
  color: ActionColor;
  permission: string;
}

/** Static class maps (Tailwind needs literal class names to compile). */
const actionColors: Record<ActionColor, string> = {
  primary: 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-500',
  info: 'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-500',
  warning: 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-500',
  success: 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500',
};

const quickActionCatalog: QuickAction[] = [
  {
    key: 'pos',
    href: route('pos.index'),
    icon: Coffee,
    title: t.quick_actions.pos.title,
    description: t.quick_actions.pos.desc,
    color: 'primary',
    permission: 'order.create',
  },
  {
    key: 'orders',
    href: route('orders.index'),
    icon: Receipt,
    title: t.quick_actions.orders.title,
    description: t.quick_actions.orders.desc,
    color: 'info',
    permission: 'order.view',
  },
  {
    key: 'products',
    href: route('products.index'),
    icon: ShoppingBag,
    title: t.quick_actions.products.title,
    description: t.quick_actions.products.desc,
    color: 'warning',
    permission: 'product.view',
  },
  {
    key: 'reports',
    href: route('reports.index'),
    icon: BarChart3,
    title: t.quick_actions.reports.title,
    description: t.quick_actions.reports.desc,
    color: 'success',
    permission: 'report.view',
  },
];

const quickActions = computed<QuickAction[]>(() =>
  quickActionCatalog.filter((action) => can(action.permission))
);

/** Placeholder roster until staff performance data comes from the backend. */
const staff = [
  { id: 'admin', initials: 'A', name: 'Admin User', metric: '15 orders processed', value: '$1,234' },
];

function itemCount(order: RecentOrder): number {
  return order.items?.length
    ? order.items.reduce((sum, item) => sum + item.quantity, 0)
    : order.items_count ?? 0;
}
</script>

<template>
  <Head :title="t.welcome_back" />

  <AuthenticatedLayout>
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 p-4 font-sans sm:p-6 lg:p-8">
      <!-- Page header -->
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
          <h1 class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
            {{ t.header.title }}
          </h1>
          <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
            <span>{{ t.welcome_back }}, {{ userName }}</span>
            <span class="hidden sm:inline" aria-hidden="true">/</span>
            <span class="inline-flex items-center gap-1.5">
              <CalendarDays class="size-4" aria-hidden="true" />
              {{ currentDate }}
            </span>
            <span class="inline-flex items-center gap-1.5">
              <Clock class="size-4" aria-hidden="true" />
              {{ currentTime }}
            </span>
          </p>
        </div>

        <Button v-if="can('order.create')" as-child>
          <Link :href="route('pos.index')">
            <Coffee aria-hidden="true" />
            {{ t.launch_pos }}
          </Link>
        </Button>
      </header>

      <!-- KPI cards -->
      <section :aria-label="t.stats.section_label" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Card v-for="stat in displayStats" :key="stat.label">
          <CardContent class="flex items-start justify-between gap-4 p-5">
            <div class="space-y-1">
              <p class="text-sm font-medium text-muted-foreground">{{ stat.label }}</p>
              <p class="text-2xl font-semibold tabular-nums tracking-tight text-foreground">
                {{ stat.value }}
              </p>
              <span
                v-if="stat.trend"
                class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/10 dark:text-success-500"
              >
                {{ stat.trend }}
              </span>
            </div>

            <span
              class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-500"
            >
              <component :is="resolveIcon(stat.icon)" class="size-5" aria-hidden="true" />
            </span>
          </CardContent>
        </Card>
      </section>

      <!-- Revenue overview (permission-gated) -->
      <Card v-if="can('report.view')">
        <CardHeader class="flex flex-row items-start justify-between gap-4 space-y-0">
          <div class="space-y-1">
            <CardTitle>{{ t.revenue.title }}</CardTitle>
            <CardDescription>{{ t.revenue.subtitle }}</CardDescription>
          </div>

          <Button variant="ghost" size="sm" as-child>
            <Link :href="route('reports.index')">
              {{ t.revenue.view_reports }}
              <ArrowRight aria-hidden="true" />
            </Link>
          </Button>
        </CardHeader>

        <CardContent>
          <div class="flex h-48 items-center justify-center rounded-lg bg-muted/50">
            <div class="text-center">
              <BarChart3 class="mx-auto mb-2 size-12 text-neutral-300 dark:text-neutral-600" aria-hidden="true" />
              <p class="text-sm text-muted-foreground">{{ t.revenue.placeholder }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Staff performance (admin only) -->
      <Card v-if="can('user.manage')">
        <CardHeader class="flex flex-row items-center justify-between gap-4 space-y-0">
          <div class="space-y-1">
            <CardTitle>{{ t.staff.title }}</CardTitle>
            <CardDescription>{{ t.staff.subtitle }}</CardDescription>
          </div>
          <Users class="size-5 text-muted-foreground" aria-hidden="true" />
        </CardHeader>

        <CardContent>
          <ul class="space-y-3">
            <li
              v-for="member in staff"
              :key="member.id"
              class="flex items-center justify-between gap-4 rounded-lg bg-muted/50 p-3"
            >
              <div class="flex min-w-0 items-center gap-3">
                <span
                  class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground"
                  aria-hidden="true"
                >
                  {{ member.initials }}
                </span>
                <div class="min-w-0">
                  <p class="truncate font-medium text-foreground">{{ member.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ member.metric }}</p>
                </div>
              </div>
              <span class="font-mono text-sm font-medium text-foreground">{{ member.value }}</span>
            </li>

            <li v-if="!staff.length" class="py-6 text-center text-sm text-muted-foreground">
              {{ t.staff.empty }}
            </li>
          </ul>
        </CardContent>
      </Card>

      <!-- Quick actions -->
      <section aria-labelledby="quick-actions-heading" class="space-y-3">
        <h2 id="quick-actions-heading" class="text-lg font-semibold tracking-tight text-foreground">
          {{ t.quick_actions.title }}
        </h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <Link
            v-for="action in quickActions"
            :key="action.key"
            :href="action.href"
            class="group rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
          >
            <Card class="h-full transition-all duration-200 group-hover:-translate-y-0.5 group-hover:border-primary-500 group-hover:shadow-md">
              <CardContent class="flex h-full flex-col items-center gap-3 p-5 text-center">
                <span
                  class="flex size-12 items-center justify-center rounded-full"
                  :class="actionColors[action.color]"
                >
                  <component :is="action.icon" class="size-6" aria-hidden="true" />
                </span>
                <span class="font-semibold text-foreground">{{ action.title }}</span>
                <span class="text-xs text-muted-foreground">{{ action.description }}</span>
              </CardContent>
            </Card>
          </Link>
        </div>
      </section>

      <!-- Recent orders -->
      <Card class="overflow-hidden">
        <CardHeader class="flex flex-row items-center justify-between gap-4 space-y-0">
          <CardTitle>{{ t.recent_orders.title }}</CardTitle>

          <Button v-if="can('order.view')" variant="ghost" size="sm" as-child>
            <Link :href="route('orders.index')">
              {{ t.recent_orders.view_all }}
              <ArrowRight aria-hidden="true" />
            </Link>
          </Button>
        </CardHeader>

        <CardContent class="p-0">
          <Table>
            <TableCaption class="sr-only">{{ t.recent_orders.title }}</TableCaption>

            <TableHeader>
              <TableRow class="hover:bg-transparent">
                <TableHead scope="col" class="pl-6">{{ t.recent_orders.columns.order_id }}</TableHead>
                <TableHead scope="col">{{ t.recent_orders.columns.time }}</TableHead>
                <TableHead scope="col" class="text-center">{{ t.recent_orders.columns.items }}</TableHead>
                <TableHead scope="col" class="text-right">{{ t.recent_orders.columns.total }}</TableHead>
                <TableHead scope="col" class="pr-6 text-center">{{ t.recent_orders.columns.status }}</TableHead>
              </TableRow>
            </TableHeader>

            <TableBody>
              <TableRow v-for="order in recentOrders" :key="order.id">
                <TableCell class="pl-6 font-medium">
                  <Link
                    :href="route('orders.show', order.id)"
                    class="text-primary-600 underline-offset-4 hover:underline dark:text-primary-500"
                  >
                    {{ order.order_number }}
                  </Link>
                </TableCell>
                <TableCell class="text-muted-foreground">{{ formatTime(order.created_at) }}</TableCell>
                <TableCell class="text-center tabular-nums">{{ itemCount(order) }}</TableCell>
                <TableCell class="text-right font-mono font-medium tabular-nums">
                  {{ money(order.total_amount) }}
                </TableCell>
                <TableCell class="pr-6 text-center">
                  <Badge
                    variant="outline"
                    class="gap-1.5 rounded-full font-medium capitalize"
                    :class="statusBadgeClass(order.status)"
                  >
                    <span
                      class="size-1.5 shrink-0 rounded-full"
                      :class="statusDotClass(order.status)"
                      aria-hidden="true"
                    />
                    {{ statusLabel(order.status) }}
                  </Badge>
                </TableCell>
              </TableRow>

              <TableRow v-if="!recentOrders.length" class="hover:bg-transparent">
                <TableCell colspan="5" class="py-10 text-center text-sm text-muted-foreground">
                  {{ t.recent_orders.empty }}
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
