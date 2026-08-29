<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { usePolling } from '@/Composables/usePolling';
import type { Report, ReportStatsData } from '@/Types/report.type';
import ReportStats from './ReportStats.vue';
import ReportForm from './ReportForm.vue';
import ReportTable from './ReportTable.vue';
import ReportDetailModal from './ReportDetailModal.vue';
import en from '@/lang/en';
import { RefreshCcw } from 'lucide-vue-next';

const props = defineProps<{
  reports: Report[];
  stats: ReportStatsData;
}>();

const t = en.report;
const selectedReport = ref<Report | null>(null);
const showModal = ref(false);
const notification = ref<{ message: string; type: 'success' | 'error' | 'info' } | null>(null);

const hasProcessing = computed(() =>
  props.reports.some((report) => ['processing', 'pending'].includes(report.status))
);

function refreshReports() {
  router.reload({ only: ['reports', 'stats'] });
}

usePolling(refreshReports, 5000, () => hasProcessing.value);

function showNotification(message: string, type: 'success' | 'error' | 'info') {
  notification.value = { message, type };
  setTimeout(() => {
    notification.value = null;
  }, 3500);
}

function handleGenerateSuccess() {
  showNotification(t.dashboard.form.queued, 'success');
  refreshReports();
}

function handleGenerateError() {
  showNotification(t.dashboard.form.error, 'error');
}

function openModal(report: Report) {
  selectedReport.value = report;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedReport.value = null;
}
</script>

<template>
  <Head :title="t.title" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <!-- Toast Notification -->
      <Transition name="slide-down">
        <div
          v-if="notification"
          class="fixed right-6 top-20 z-50 flex max-w-sm items-center gap-3 rounded-xl border bg-surface px-4 py-3 text-sm font-medium shadow-md"
          :class="
            notification.type === 'success'
              ? 'border-success-500 text-success-600 dark:border-success-500'
              : notification.type === 'error'
              ? 'border-danger-500 text-danger-600 dark:border-danger-500'
              : 'border-info-500 text-info-600 dark:border-info-500'
          "
        >
          {{ notification.message }}
        </div>
      </Transition>

      <!-- Page Header -->
      <PageHeader
        :eyebrow="t.dashboard.eyebrow"
        :title="t.title"
        :subtitle="t.dashboard.subtitle"
      >
        <template #actions>
          <BaseButton
            variant="secondary"
            size="md"
            :icon="RefreshCcw"
            @click="refreshReports"
          >
            {{ t.dashboard.refresh }}
          </BaseButton>
        </template>
      </PageHeader>

      <!-- KPI Stats Strip -->
      <ReportStats :stats="stats" :has-processing="hasProcessing" />

      <!-- Form and Table Grid -->
      <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-1">
          <ReportForm
            @success="handleGenerateSuccess"
            @error="handleGenerateError"
          />
        </div>

        <div class="xl:col-span-2">
          <ReportTable
            :reports="reports"
            :has-processing="hasProcessing"
            @view="openModal"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

  <!-- Report Detail Modal -->
  <ReportDetailModal
    :show="showModal"
    :report="selectedReport"
    @close="closeModal"
  />
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.2s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-0.5rem);
}
</style>
