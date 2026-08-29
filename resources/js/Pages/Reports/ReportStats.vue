<script setup lang="ts">
import StatCard from '@/Components/StatCard.vue';
import BaseCard from '@/Components/BaseCard.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import BaseText from '@/Components/BaseText.vue';
import { useReportStats } from '@/Composables/useReportStats';
import type { ReportStatsData } from '@/Types/report.type';
import en from '@/lang/en';
import {
  CheckCircle2,
  FileText,
  RefreshCcw,
  XCircle,
} from 'lucide-vue-next';

const props = defineProps<{
  stats: ReportStatsData;
  hasProcessing: boolean;
}>();

const t = en.report;
const { readyRate, queueCount } = useReportStats(props.stats);
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <StatCard
      :label="t.dashboard.stats.total"
      :value="stats.total"
      :hint="t.dashboard.stats.total_hint"
      :icon="FileText"
    />

    <StatCard
      :label="t.dashboard.stats.ready"
      :value="stats.done"
      :hint="`${readyRate}% ${t.dashboard.stats.ready_hint}`"
      :icon="CheckCircle2"
      icon-color-class="text-success-600"
    />

    <BaseCard>
      <CardHeader :title="t.dashboard.stats.queue" :subtitle="t.dashboard.stats.queue_hint">
        <template #actions>
          <RefreshCcw class="h-5 w-5 text-info-600" :class="hasProcessing ? 'animate-spin' : ''" />
        </template>
      </CardHeader>

      <CardContent>
        <BaseText size="2xl" weight="semibold">{{ queueCount }}</BaseText>
      </CardContent>
    </BaseCard>

    <StatCard
      :label="t.dashboard.stats.failed"
      :value="stats.failed"
      :hint="t.dashboard.stats.failed_hint"
      :icon="XCircle"
      icon-color-class="text-danger-600"
    />
  </div>
</template>
