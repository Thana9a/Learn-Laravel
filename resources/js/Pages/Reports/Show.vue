<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseCard from '@/Components/BaseCard.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseText from '@/Components/BaseText.vue';
import StatMiniCard from '@/Components/StatMiniCard.vue';
import BaseListRow from '@/Components/BaseListRow.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import { useReportResults } from '@/Composables/useReportResults';
import en from '@/lang/en';
import { ArrowLeft, Calendar, FileText } from 'lucide-vue-next';
import type { Report } from '@/Types/report.type';

defineProps<{
  report: Report;
}>();

const t = en.report;
const common = en.common;
const { money } = useReportResults();
</script>

<template>
  <Head :title="report.title" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
      <div class="flex items-center justify-between gap-4">
        <BaseButton
          variant="secondary"
          size="md"
          :icon="ArrowLeft"
          :href="route('reports.index')"
        >
          {{ common.actions.back }}
        </BaseButton>
      </div>

      <BaseCard padding="lg" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-border pb-6 gap-4">
          <div>
            <div class="flex items-center gap-3">
              <BaseText as="h1" size="2xl" weight="semibold">
                {{ report.title }}
              </BaseText>
              <BaseBadge :status="report.status" />
            </div>
            <BaseText size="xs" variant="muted" class="mt-1 capitalize">
              {{ t.dashboard.table.type }}: {{ report.type }}
            </BaseText>
          </div>
        </div>

        <div v-if="report.result" class="space-y-6">
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

          <BaseCard :flush="true">
            <CardHeader :title="t.dashboard.results.top_categories" />

            <CardContent>
              <div v-if="report.result.top_categories?.length" class="divide-y divide-border">
                <BaseListRow
                  v-for="cat in report.result.top_categories"
                  :key="cat.category"
                  :label="cat.category"
                  :sublabel="`${cat.count} ${t.dashboard.results.items_sold}`"
                  :value="money(cat.revenue)"
                  mono
                />
              </div>
              <div v-else class="px-4 py-8 text-center">
                <BaseText size="sm" variant="muted" align="center">{{ t.dashboard.results.empty_categories }}</BaseText>
              </div>
            </CardContent>
          </BaseCard>
        </div>

        <div v-else class="text-center py-12">
          <FileText class="h-12 w-12 mx-auto text-neutral-300 dark:text-neutral-600 mb-2" />
          <BaseText size="sm" variant="muted" align="center">{{ t.dashboard.table.empty }}</BaseText>
        </div>
      </BaseCard>
    </div>
  </AuthenticatedLayout>
</template>
