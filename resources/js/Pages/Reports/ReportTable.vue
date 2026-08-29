<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import BaseTable from '@/Components/BaseTable.vue';
import BaseTableRow from '@/Components/BaseTableRow.vue';
import BaseTableCell from '@/Components/BaseTableCell.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseText from '@/Components/BaseText.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import { useReportsTable } from '@/Composables/useReportsTable';
import type { Report } from '@/Types/report.type';
import en from '@/lang/en';
import { Eye } from 'lucide-vue-next';

defineProps<{
  reports: Report[];
  hasProcessing: boolean;
}>();

const emit = defineEmits<{
  (e: 'view', report: Report): void;
}>();

const t = en.report;
const common = en.common;
const { columns, canView } = useReportsTable();
</script>

<template>
  <BaseCard :flush="true">
    <CardHeader :title="t.dashboard.table.title" :subtitle="t.dashboard.table.subtitle">
      <template #actions>
        <BaseBadge v-if="hasProcessing" status="processing" :pulse="true">
          {{ t.dashboard.table.processing }}
        </BaseBadge>
      </template>
    </CardHeader>

    <CardContent>
      <BaseTable :empty="reports.length === 0" :empty-message="t.dashboard.table.empty">
      <template #header>
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-4 py-3 font-medium"
            :class="col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'"
          >
            {{ col.label }}
          </th>
        </tr>
      </template>

      <BaseTableRow
        v-for="report in reports"
        :key="report.id"
      >
        <BaseTableCell>
          <BaseText weight="medium">{{ report.title }}</BaseText>
          <BaseText v-if="report.generated_at" size="xs" variant="muted">
            {{ t.dashboard.table.generated }} {{ report.generated_at }}
          </BaseText>
        </BaseTableCell>
        <BaseTableCell class="capitalize">{{ report.type }}</BaseTableCell>
        <BaseTableCell align="center">
          <BaseBadge :status="report.status" />
        </BaseTableCell>
        <BaseTableCell size="xs" muted>{{ report.created_at }}</BaseTableCell>
        <BaseTableCell align="right">
          <BaseButton
            v-if="canView(report)"
            variant="ghost"
            size="icon"
            :icon="Eye"
            :aria-label="`${common.actions.view} ${report.title}`"
            @click="emit('view', report)"
          />
          <BaseText v-else size="xs" variant="muted" class="italic">
            {{ report.status }}
          </BaseText>
        </BaseTableCell>
      </BaseTableRow>
      </BaseTable>
    </CardContent>
  </BaseCard>
</template>
