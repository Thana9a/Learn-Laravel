import { computed, onMounted, onUnmounted, ref, type Component } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { BarChart3, Package, Receipt, TrendingUp } from 'lucide-vue-next';
import en from '@/lang/en';

/** A generic KPI stat for the dashboard. */
export interface DashboardStat {
  label: string;
  value: string | number;
  trend?: string;
  icon?: string | Component;
}

const t = en.dashboard;

const iconComponents: Record<string, Component> = {
  TrendingUp,
  Receipt,
  Package,
  BarChart3,
};

/**
 * useDashboard — derives the greeting name, live clock, and fallback stats for
 * the dashboard screen so Dashboard.vue stays presentational.
 */
export function useDashboard(stats: DashboardStat[]) {
  const page = usePage();

  const userName = computed<string>(() => {
    const auth = page.props.auth as { user?: { name?: string } } | undefined;
    return auth?.user?.name ?? 'User';
  });

  const displayStats = computed<DashboardStat[]>(() => {
    if (stats.length > 0) return stats;

    return [
      { label: t.stats.today_sales, value: '$0.00', trend: '+0%', icon: TrendingUp },
      { label: t.stats.orders_today, value: '0', trend: '+0%', icon: Receipt },
      { label: t.stats.active_products, value: '0', icon: Package },
    ];
  });

  function resolveIcon(icon?: string | Component): Component {
    if (typeof icon === 'string') {
      return iconComponents[icon] ?? BarChart3;
    }
    return icon ?? BarChart3;
  }

  const currentDate = ref('');
  const currentTime = ref('');
  let timer: ReturnType<typeof setInterval> | undefined;

  function updateTime() {
    const now = new Date();
    currentDate.value = now.toLocaleDateString(undefined, {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    currentTime.value = now.toLocaleTimeString(undefined, {
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  onMounted(() => {
    updateTime();
    timer = setInterval(updateTime, 1000);
  });

  onUnmounted(() => {
    if (timer) clearInterval(timer);
  });

  return {
    userName,
    displayStats,
    resolveIcon,
    currentDate,
    currentTime,
  };
}
