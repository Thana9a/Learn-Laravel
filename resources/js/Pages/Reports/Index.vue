<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, type Component } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import en from '@/lang/en';
import {
    Activity,
    AlertTriangle,
    BarChart3,
    CheckCircle2,
    Eye,
    FileText,
    Package,
    RefreshCcw,
    Send,
    TrendingUp,
    X,
    XCircle,
} from 'lucide-vue-next';

interface ReportResult {
    total_sales: number;
    total_orders: number;
    average_order_value: number;
    total_products: number;
    top_categories: { category: string; count: number; revenue: number }[];
    generated_at: string;
}

interface Report {
    id: number;
    title: string;
    type: string;
    status: 'pending' | 'processing' | 'done' | 'failed';
    generated_at: string | null;
    created_at: string;
    result: ReportResult | null;
}

interface Stats {
    total: number;
    done: number;
    processing: number;
    failed: number;
    pending: number;
}

interface ReportType {
    value: 'sales' | 'inventory' | 'performance';
    label: string;
    description: string;
    icon: Component;
}

const props = defineProps<{
    reports: Report[];
    stats: Stats;
}>();

const t = en.report;
const common = en.common;
const selectedReport = ref<Report | null>(null);
const showModal = ref(false);
const notification = ref<{ message: string; type: 'success' | 'error' | 'info' } | null>(null);
let pollInterval: ReturnType<typeof setInterval> | null = null;

const form = useForm({
    title: '',
    type: 'sales',
});

const reportTypes: ReportType[] = [
    { value: 'sales', label: t.dashboard.types.sales, description: t.dashboard.types.sales_desc, icon: TrendingUp },
    { value: 'inventory', label: t.dashboard.types.inventory, description: t.dashboard.types.inventory_desc, icon: Package },
    { value: 'performance', label: t.dashboard.types.performance, description: t.dashboard.types.performance_desc, icon: Activity },
];

const hasProcessing = computed(() => props.reports.some((report) => ['processing', 'pending'].includes(report.status)));
const readyRate = computed(() => {
    if (props.stats.total === 0) {
        return 0;
    }

    return Math.round((props.stats.done / props.stats.total) * 100);
});

function submitGenerate() {
    form.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.type = 'sales';
            showNotification(t.dashboard.form.queued, 'success');
            refreshReports();
        },
        onError: () => showNotification(t.dashboard.form.error, 'error'),
    });
}

function refreshReports() {
    router.reload({ only: ['reports', 'stats'], preserveScroll: true });
}

function showNotification(message: string, type: 'success' | 'error' | 'info') {
    notification.value = { message, type };
    setTimeout(() => {
        notification.value = null;
    }, 3500);
}

function openModal(report: Report) {
    selectedReport.value = report;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedReport.value = null;
}

function statusClasses(status: Report['status']): string {
    switch (status) {
        case 'done':
            return 'bg-success-50 text-success-600';
        case 'processing':
            return 'bg-info-50 text-info-600';
        case 'pending':
            return 'bg-warning-50 text-warning-600';
        case 'failed':
            return 'bg-danger-50 text-danger-600';
    }
}

function statusDotClasses(status: Report['status']): string {
    switch (status) {
        case 'done':
            return 'bg-success-500';
        case 'processing':
            return 'bg-info-500';
        case 'pending':
            return 'bg-warning-500';
        case 'failed':
            return 'bg-danger-500';
    }
}

function statusIcon(status: Report['status']): Component {
    switch (status) {
        case 'done':
            return CheckCircle2;
        case 'failed':
            return XCircle;
        case 'processing':
            return RefreshCcw;
        case 'pending':
            return AlertTriangle;
    }
}

function fmt(n: number | undefined, decimals = 2): string {
    if (n === undefined || n === null) {
        return '-';
    }

    return Number(n).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

onMounted(() => {
    pollInterval = setInterval(() => {
        if (hasProcessing.value) {
            refreshReports();
        }
    }, 5000);
});

onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
</script>

<template>
    <Head :title="t.title" />

    <AuthenticatedLayout>
        <div class="space-y-6 p-4 sm:p-6 lg:p-8">
            <Transition name="slide-down">
                <div
                    v-if="notification"
                    class="fixed right-6 top-20 z-50 flex max-w-sm items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm font-medium shadow-sm"
                    :class="notification.type === 'success' ? 'border-success-500 text-success-600' : notification.type === 'error' ? 'border-danger-500 text-danger-600' : 'border-info-500 text-info-600'"
                >
                    {{ notification.message }}
                </div>
            </Transition>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">{{ t.dashboard.eyebrow }}</p>
                    <h1 class="mt-1 text-2xl font-semibold text-neutral-900">{{ t.title }}</h1>
                    <p class="mt-1 text-sm text-neutral-500">{{ t.dashboard.subtitle }}</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-neutral-100 px-4 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200"
                    @click="refreshReports"
                >
                    <RefreshCcw class="h-4 w-4" :class="hasProcessing ? 'animate-spin' : ''" />
                    {{ t.dashboard.refresh }}
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.total }}</p>
                        <FileText class="h-5 w-5 text-primary-500" />
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ stats.total }}</p>
                    <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.total_hint }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.ready }}</p>
                        <CheckCircle2 class="h-5 w-5 text-success-600" />
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ stats.done }}</p>
                    <p class="mt-1 text-xs text-neutral-500">{{ readyRate }}% {{ t.dashboard.stats.ready_hint }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.queue }}</p>
                        <RefreshCcw class="h-5 w-5 text-info-600" :class="hasProcessing ? 'animate-spin' : ''" />
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ stats.processing + stats.pending }}</p>
                    <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.queue_hint }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-neutral-500">{{ t.dashboard.stats.failed }}</p>
                        <XCircle class="h-5 w-5 text-danger-600" />
                    </div>
                    <p class="mt-2 text-2xl font-semibold text-neutral-900">{{ stats.failed }}</p>
                    <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.stats.failed_hint }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <section class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm xl:col-span-1">
                    <div class="mb-4">
                        <h2 class="text-lg font-medium text-neutral-900">{{ t.dashboard.form.title }}</h2>
                        <p class="mt-1 text-xs text-neutral-500">{{ t.dashboard.form.subtitle }}</p>
                    </div>

                    <form class="space-y-4" @submit.prevent="submitGenerate">
                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-neutral-700">{{ t.dashboard.form.report_title }}</span>
                            <input
                                v-model="form.title"
                                type="text"
                                :placeholder="t.dashboard.form.placeholder"
                                class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            />
                            <span v-if="form.errors.title" class="mt-1 block text-xs text-danger-600">{{ form.errors.title }}</span>
                        </label>

                        <div>
                            <span class="mb-2 block text-xs font-medium text-neutral-700">{{ t.dashboard.form.type }}</span>
                            <div class="space-y-2">
                                <label
                                    v-for="reportType in reportTypes"
                                    :key="reportType.value"
                                    class="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-colors"
                                    :class="form.type === reportType.value ? 'border-primary-500 bg-primary-50 text-primary-700 ring-1 ring-primary-500' : 'border-neutral-200 bg-neutral-50 text-neutral-700 hover:bg-neutral-100'"
                                >
                                    <input v-model="form.type" type="radio" :value="reportType.value" class="sr-only" />
                                    <component :is="reportType.icon" class="h-4 w-4" />
                                    <span class="min-w-0">
                                        <span class="block font-medium">{{ reportType.label }}</span>
                                        <span class="block text-xs text-neutral-500">{{ reportType.description }}</span>
                                    </span>
                                </label>
                            </div>
                            <span v-if="form.errors.type" class="mt-1 block text-xs text-danger-600">{{ form.errors.type }}</span>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-primary-500 px-4 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Send class="h-4 w-4" />
                            {{ form.processing ? t.dashboard.form.queuing : t.dashboard.form.generate }}
                        </button>
                    </form>
                </section>

                <section class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm xl:col-span-2">
                    <div class="flex items-center justify-between border-b border-neutral-200 px-4 py-3">
                        <div>
                            <h2 class="text-lg font-medium text-neutral-900">{{ t.dashboard.table.title }}</h2>
                            <p class="text-xs text-neutral-500">{{ t.dashboard.table.subtitle }}</p>
                        </div>
                        <span
                            v-if="hasProcessing"
                            class="inline-flex items-center gap-1 rounded-full bg-info-50 px-2.5 py-0.5 text-xs font-medium text-info-600"
                        >
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-info-500" />
                            {{ t.dashboard.table.processing }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="sticky top-0 border-b border-neutral-200 bg-white text-xs text-neutral-500">
                                <tr>
                                    <th class="px-4 py-3 font-medium">{{ t.dashboard.table.report }}</th>
                                    <th class="px-4 py-3 font-medium">{{ t.dashboard.table.type }}</th>
                                    <th class="px-4 py-3 text-center font-medium">{{ t.dashboard.table.status }}</th>
                                    <th class="px-4 py-3 font-medium">{{ t.dashboard.table.created }}</th>
                                    <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.actions }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                <tr v-if="reports.length === 0">
                                    <td colspan="5" class="px-4 py-10 text-center text-neutral-500">
                                        {{ t.dashboard.table.empty }}
                                    </td>
                                </tr>
                                <tr v-for="report in reports" :key="report.id" class="h-12 hover:bg-neutral-50">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-neutral-900">{{ report.title }}</p>
                                        <p v-if="report.generated_at" class="text-xs text-neutral-500">{{ t.dashboard.table.generated }} {{ report.generated_at }}</p>
                                    </td>
                                    <td class="px-4 py-3 capitalize">{{ report.type }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusClasses(report.status)">
                                            <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClasses(report.status)" />
                                            {{ report.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-neutral-500">{{ report.created_at }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            v-if="report.status === 'done' && report.result"
                                            type="button"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-900"
                                            :aria-label="`${common.actions.view} ${report.title}`"
                                            @click="openModal(report)"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <component v-else :is="statusIcon(report.status)" class="ml-auto h-4 w-4 text-neutral-400" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>

    <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm" type="button" :aria-label="t.dashboard.results.close_label" @click="closeModal" />

            <div class="relative w-full max-w-2xl overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-xl">
                <div class="flex items-start justify-between gap-4 border-b border-neutral-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-medium text-neutral-900">{{ selectedReport?.title }}</h3>
                        <p class="text-xs text-neutral-500">{{ t.dashboard.results.generated }} {{ selectedReport?.generated_at }}</p>
                    </div>
                    <button type="button" class="rounded-lg p-2 text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-900" @click="closeModal">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div v-if="selectedReport?.result" class="space-y-5 p-6">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                            <p class="text-xs text-neutral-500">{{ t.dashboard.results.sales }}</p>
                            <p class="mt-1 text-xl font-semibold text-neutral-900">${{ fmt(selectedReport.result.total_sales) }}</p>
                        </div>
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                            <p class="text-xs text-neutral-500">{{ t.dashboard.results.orders }}</p>
                            <p class="mt-1 text-xl font-semibold text-neutral-900">{{ selectedReport.result.total_orders }}</p>
                        </div>
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                            <p class="text-xs text-neutral-500">{{ t.dashboard.results.average_order }}</p>
                            <p class="mt-1 text-xl font-semibold text-neutral-900">${{ fmt(selectedReport.result.average_order_value) }}</p>
                        </div>
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                            <p class="text-xs text-neutral-500">{{ t.dashboard.results.products }}</p>
                            <p class="mt-1 text-xl font-semibold text-neutral-900">{{ selectedReport.result.total_products }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-neutral-200">
                        <div class="border-b border-neutral-200 px-4 py-3">
                            <h4 class="text-sm font-medium text-neutral-900">{{ t.dashboard.results.top_categories }}</h4>
                        </div>
                        <div v-if="selectedReport.result.top_categories?.length" class="divide-y divide-neutral-100">
                            <div
                                v-for="category in selectedReport.result.top_categories"
                                :key="category.category"
                                class="flex items-center justify-between gap-4 px-4 py-3 text-sm"
                            >
                                <div>
                                    <p class="font-medium text-neutral-900">{{ category.category }}</p>
                                    <p class="text-xs text-neutral-500">{{ category.count }} {{ t.dashboard.results.items_sold }}</p>
                                </div>
                                <p class="font-mono font-medium text-neutral-900">${{ fmt(category.revenue) }}</p>
                            </div>
                        </div>
                        <div v-else class="px-4 py-8 text-center text-sm text-neutral-500">
                            {{ t.dashboard.results.empty_categories }}
                        </div>
                    </div>
                </div>

                <div class="border-t border-neutral-200 px-6 py-4">
                    <button
                        type="button"
                        class="inline-flex h-10 items-center rounded-lg bg-neutral-100 px-4 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200"
                        @click="closeModal"
                    >
                        {{ common.actions.close }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active,
.fade-enter-active,
.fade-leave-active {
    transition: all 0.18s ease;
}

.slide-down-enter-from,
.slide-down-leave-to,
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-down-enter-from,
.slide-down-leave-to {
    transform: translateY(-0.5rem);
}
</style>
