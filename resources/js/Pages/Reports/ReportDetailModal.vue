<script setup lang="ts">
import BaseModal from '@/Components/BaseModal.vue';
import BaseCard from '@/Components/BaseCard.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseText from '@/Components/BaseText.vue';
import StatMiniCard from '@/Components/StatMiniCard.vue';
import BaseListRow from '@/Components/BaseListRow.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import { useReportResults } from '@/Composables/useReportResults';
import type { Report } from '@/Types/report.type';
import en from '@/lang/en';

defineProps<{
  show: boolean;
  report: Report | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const t = en.report;
const common = en.common;
const { money } = useReportResults();
</script>

<template>
  <BaseModal
    :show="show"
    :title="report?.title"
    :subtitle="report?.generated_at ? `${t.dashboard.results.generated} ${report.generated_at}` : undefined"
    max-width="2xl"
    @close="emit('close')"
  >
    <div v-if="report?.result" class="space-y-6">
      <div class="flex items-center justify-between">
        <BaseBadge :status="report.status" />
        <BaseText size="xs" variant="muted" class="capitalize">
          {{ t.dashboard.table.type }}: {{ report.type }}
        </BaseText>
      </div>

      <!-- Result KPI Grid -->
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <StatMiniCard
          :label="t.dashboard.results.sales"
          :value="money(report.result.total_sales)"
          mono
        />

        <StatMiniCard
          :label="t.dashboard.results.orders"
          :value="report.result.total_orders"
        />

        <StatMiniCard
          :label="t.dashboard.results.average_order"
          :value="money(report.result.average_order_value)"
          mono
        />

        <StatMiniCard
          :label="t.dashboard.results.products"
          :value="report.result.total_products"
        />
      </div>

      <!-- Top Categories Breakdown -->
      <BaseCard :flush="true">
        <CardHeader :title="t.dashboard.results.top_categories" />

        <CardContent>
          <div v-if="report.result.top_categories?.length" class="divide-y divide-border">
            <BaseListRow
              v-for="category in report.result.top_categories"
              :key="category.category"
              :label="category.category"
              :sublabel="`${category.count} ${t.dashboard.results.items_sold}`"
              :value="money(category.revenue)"
              mono
            />
          </div>
          <div v-else class="px-4 py-8 text-center">
            <BaseText size="sm" variant="muted" align="center">{{ t.dashboard.results.empty_categories }}</BaseText>
          </div>
        </CardContent>
      </BaseCard>
    </div>

    <template #footer>
      <div class="flex justify-end w-full">
        <BaseButton variant="secondary" @click="emit('close')">
          {{ common.actions.close }}
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>
