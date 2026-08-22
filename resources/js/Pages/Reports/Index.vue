<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

// ─── Props from Inertia (controller) ─────────────────────────────────────────

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

const props = defineProps<{
    reports: Report[];
    stats: Stats;
}>();

// ─── Local State ─────────────────────────────────────────────────────────────

const reportsList  = ref<Report[]>(props.reports);
const statsData    = ref<Stats>(props.stats);
const selectedReport = ref<Report | null>(null);
const showModal    = ref(false);
const notification = ref<{ message: string; type: 'success' | 'error' | 'info' } | null>(null);

// ─── Form (Inertia useForm) ───────────────────────────────────────────────────

const form = useForm({
    title: '',
    type: 'sales',
});

const reportTypes = [
    { value: 'sales',       label: 'Sales Report',       icon: '📈' },
    { value: 'inventory',   label: 'Inventory Report',   icon: '📦' },
    { value: 'performance', label: 'Performance Report', icon: '🚀' },
];

function submitGenerate() {
    form.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showNotification('Report queued! Processing in the background...', 'success');
            refreshReports();
        },
    });
}

// ─── WebSocket / Real-time (Laravel Echo + Reverb) ───────────────────────────
// NOTE: Requires reverb to be installed and BROADCAST_CONNECTION=reverb in .env
// Falls back to polling if Echo is unavailable.

let echoChannel: any = null;
let pollInterval: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    // Try to connect via Echo/Reverb for real-time updates
    if (typeof (window as any).Echo !== 'undefined') {
        const userId = (window as any).__INERTIA_PAGE_PROPS__?.auth?.user?.id;
        if (userId) {
            echoChannel = (window as any).Echo
                .private(`reports.${userId}`)
                .listen('.report.generated', (data: any) => {
                    handleRealtimeUpdate(data);
                });
        }
    } else {
        // Fallback: poll every 5 seconds if any report is processing/pending
        pollInterval = setInterval(() => {
            const hasActive = reportsList.value.some(
                r => r.status === 'processing' || r.status === 'pending'
            );
            if (hasActive) refreshReports();
        }, 5000);
    }
});

onUnmounted(() => {
    if (echoChannel) echoChannel.stopListening('.report.generated');
    if (pollInterval) clearInterval(pollInterval);
});

function handleRealtimeUpdate(data: any) {
    const idx = reportsList.value.findIndex(r => r.id === data.id);
    if (idx !== -1) {
        reportsList.value[idx] = {
            ...reportsList.value[idx],
            status:       data.status,
            generated_at: data.generated_at ? 'just now' : null,
            result:       data.result,
        };
        // Update stats
        recalcStats();
    }
    showNotification(`Report "${data.title}" is ready!`, 'success');
}

function refreshReports() {
    router.reload({ only: ['reports', 'stats'], preserveScroll: true });
}

function recalcStats() {
    const list = reportsList.value;
    statsData.value = {
        total:      list.length,
        done:       list.filter(r => r.status === 'done').length,
        processing: list.filter(r => r.status === 'processing').length,
        failed:     list.filter(r => r.status === 'failed').length,
        pending:    list.filter(r => r.status === 'pending').length,
    };
}

// ─── UI Helpers ──────────────────────────────────────────────────────────────

function showNotification(message: string, type: 'success' | 'error' | 'info') {
    notification.value = { message, type };
    setTimeout(() => { notification.value = null; }, 4000);
}

function openModal(report: Report) {
    selectedReport.value = report;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedReport.value = null;
}

const statusConfig: Record<string, { label: string; color: string; dot: string; icon: string }> = {
    pending:    { label: 'Pending',    color: 'text-amber-400  bg-amber-400/10  border-amber-400/20',  dot: 'bg-amber-400',  icon: '⏳' },
    processing: { label: 'Processing', color: 'text-blue-400   bg-blue-400/10   border-blue-400/20',   dot: 'bg-blue-400',   icon: '⚙️' },
    done:       { label: 'Done',       color: 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20', dot: 'bg-emerald-400', icon: '✅' },
    failed:     { label: 'Failed',     color: 'text-red-400    bg-red-400/10    border-red-400/20',    dot: 'bg-red-400',    icon: '❌' },
};

function fmt(n: number | undefined, decimals = 2): string {
    if (n === undefined || n === null) return '—';
    return Number(n).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

const hasProcessing = computed(() =>
    reportsList.value.some(r => r.status === 'processing' || r.status === 'pending')
);
</script>

<template>
    <Head title="Reports Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/20">
                        <span class="text-lg">📊</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Reports Dashboard</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Queue · Job · Event · Listener · Reverb</p>
                    </div>
                </div>
                <!-- Live indicator -->
                <div v-if="hasProcessing" class="flex items-center gap-2 rounded-full bg-blue-500/10 px-3 py-1 text-xs text-blue-400 border border-blue-400/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-400"></span>
                    </span>
                    Processing...
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- ── Notification toast ──────────────────────────────────────── -->
                <Transition name="slide-down">
                    <div v-if="notification"
                         :class="[
                             'fixed top-6 right-6 z-50 flex items-center gap-3 rounded-2xl border px-5 py-3 shadow-2xl backdrop-blur-md text-sm font-medium transition-all',
                             notification.type === 'success' ? 'bg-emerald-900/80 border-emerald-500/30 text-emerald-300' :
                             notification.type === 'error'   ? 'bg-red-900/80    border-red-500/30    text-red-300'     :
                                                               'bg-slate-800/80  border-slate-500/30  text-slate-300'
                         ]">
                        <span>{{ notification.type === 'success' ? '✅' : notification.type === 'error' ? '❌' : 'ℹ️' }}</span>
                        {{ notification.message }}
                    </div>
                </Transition>

                <!-- ── Stat cards ──────────────────────────────────────────────── -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <!-- Total -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 p-5 hover:border-indigo-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Total Reports</p>
                        <p class="mt-2 text-4xl font-black text-white">{{ statsData.total }}</p>
                        <div class="mt-3 flex h-1 rounded-full bg-slate-700 overflow-hidden">
                            <div class="bg-indigo-500 rounded-full transition-all duration-700" :style="{ width: statsData.total ? '100%' : '0%' }"></div>
                        </div>
                    </div>
                    <!-- Done -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 p-5 hover:border-emerald-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Completed</p>
                        <p class="mt-2 text-4xl font-black text-emerald-400">{{ statsData.done }}</p>
                        <div class="mt-3 flex h-1 rounded-full bg-slate-700 overflow-hidden">
                            <div class="bg-emerald-500 rounded-full transition-all duration-700"
                                 :style="{ width: statsData.total ? (statsData.done / statsData.total * 100) + '%' : '0%' }"></div>
                        </div>
                    </div>
                    <!-- Processing -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 p-5 hover:border-blue-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Processing</p>
                        <p class="mt-2 text-4xl font-black text-blue-400">{{ statsData.processing + statsData.pending }}</p>
                        <div class="mt-3 flex h-1 rounded-full bg-slate-700 overflow-hidden">
                            <div class="bg-blue-500 rounded-full animate-pulse" style="width: 60%"></div>
                        </div>
                    </div>
                    <!-- Failed -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/50 p-5 hover:border-red-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-red-500/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Failed</p>
                        <p class="mt-2 text-4xl font-black text-red-400">{{ statsData.failed }}</p>
                        <div class="mt-3 flex h-1 rounded-full bg-slate-700 overflow-hidden">
                            <div class="bg-red-500 rounded-full transition-all duration-700"
                                 :style="{ width: statsData.total ? (statsData.failed / statsData.total * 100) + '%' : '0%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- ── Generate Report Form + Table ───────────────────────────── -->
                <div class="grid gap-6 lg:grid-cols-3">

                    <!-- Generate form -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl border border-slate-700/50 bg-gradient-to-b from-slate-800 to-slate-900 p-6 shadow-xl">
                            <h3 class="mb-1 text-sm font-bold uppercase tracking-widest text-slate-300">Generate Report</h3>
                            <p class="mb-5 text-xs text-slate-500">
                                Triggers: <span class="text-indigo-400">Service → Job → Queue → Event → Listener → Reverb</span>
                            </p>

                            <form @submit.prevent="submitGenerate" class="space-y-4">
                                <!-- Title -->
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-400">Report Title</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        placeholder="e.g. Q3 Sales Summary"
                                        class="w-full rounded-xl border border-slate-600/50 bg-slate-700/50 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none ring-0 transition focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/20"
                                    />
                                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-400">{{ form.errors.title }}</p>
                                </div>

                                <!-- Type -->
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-400">Report Type</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        <label
                                            v-for="rt in reportTypes"
                                            :key="rt.value"
                                            :class="[
                                                'flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-2.5 text-sm transition-all duration-200',
                                                form.type === rt.value
                                                    ? 'border-indigo-500/50 bg-indigo-500/10 text-white'
                                                    : 'border-slate-600/40 bg-slate-700/30 text-slate-400 hover:border-slate-500/60 hover:text-slate-200',
                                            ]"
                                        >
                                            <input
                                                type="radio"
                                                :value="rt.value"
                                                v-model="form.type"
                                                class="hidden"
                                            />
                                            <span class="text-base">{{ rt.icon }}</span>
                                            <span class="font-medium">{{ rt.label }}</span>
                                            <span v-if="form.type === rt.value" class="ml-auto text-indigo-400">✓</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed disabled:scale-100"
                                >
                                    <span class="relative z-10 flex items-center justify-center gap-2">
                                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        <span>{{ form.processing ? 'Queuing...' : '🚀 Generate Report' }}</span>
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </button>
                            </form>

                            <!-- Architecture legend -->
                            <div class="mt-6 rounded-xl border border-slate-700/40 bg-slate-800/50 p-4">
                                <p class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-500">Architecture Flow</p>
                                <div class="space-y-1.5 text-xs text-slate-400">
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">①</span> HTTP → <span class="text-white">Controller</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">②</span> Controller → <span class="text-white">Service</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">③</span> Service → <span class="text-white">Repository + DTO</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">④</span> Service dispatches <span class="text-white">Job → Queue</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">⑤</span> Job fires <span class="text-white">Event</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">⑥</span> Event → <span class="text-white">Listener</span></div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">⑦</span> Broadcast via <span class="text-white">Reverb</span> → Vue</div>
                                    <div class="flex items-center gap-2"><span class="text-indigo-400 font-mono">⑧</span> <span class="text-white">Command + Schedule</span> auto-runs</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports table -->
                    <div class="lg:col-span-2">
                        <div class="overflow-hidden rounded-2xl border border-slate-700/50 bg-gradient-to-b from-slate-800 to-slate-900 shadow-xl">
                            <div class="flex items-center justify-between border-b border-slate-700/50 px-6 py-4">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-300">Recent Reports</h3>
                                <button
                                    @click="refreshReports"
                                    class="flex items-center gap-1.5 rounded-lg border border-slate-600/40 bg-slate-700/40 px-3 py-1.5 text-xs text-slate-400 transition hover:border-slate-500/60 hover:text-slate-200"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Refresh
                                </button>
                            </div>

                            <!-- Empty state -->
                            <div v-if="reportsList.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                                <div class="mb-4 text-5xl opacity-30">📋</div>
                                <p class="text-sm font-medium text-slate-400">No reports yet</p>
                                <p class="mt-1 text-xs text-slate-600">Generate your first report using the form</p>
                            </div>

                            <!-- Table -->
                            <div v-else class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-700/50">
                                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Report</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Created</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700/30">
                                        <tr
                                            v-for="report in reportsList"
                                            :key="report.id"
                                            class="group transition-colors duration-150 hover:bg-slate-700/20"
                                        >
                                            <!-- Title -->
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-white">{{ report.title }}</div>
                                                <div v-if="report.generated_at" class="mt-0.5 text-xs text-slate-500">
                                                    Done {{ report.generated_at }}
                                                </div>
                                            </td>
                                            <!-- Type badge -->
                                            <td class="px-4 py-4">
                                                <span class="rounded-md bg-slate-700/60 px-2 py-0.5 text-xs font-medium text-slate-300 capitalize">
                                                    {{ report.type }}
                                                </span>
                                            </td>
                                            <!-- Status badge with pulse for active -->
                                            <td class="px-4 py-4">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold',
                                                        statusConfig[report.status]?.color,
                                                    ]"
                                                >
                                                    <span
                                                        :class="[
                                                            'h-1.5 w-1.5 rounded-full',
                                                            statusConfig[report.status]?.dot,
                                                            (report.status === 'processing' || report.status === 'pending') ? 'animate-pulse' : '',
                                                        ]"
                                                    ></span>
                                                    {{ statusConfig[report.status]?.label }}
                                                </span>
                                            </td>
                                            <!-- Created -->
                                            <td class="px-4 py-4 text-xs text-slate-400">{{ report.created_at }}</td>
                                            <!-- Actions -->
                                            <td class="px-4 py-4">
                                                <button
                                                    v-if="report.status === 'done' && report.result"
                                                    @click="openModal(report)"
                                                    class="rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-3 py-1.5 text-xs font-semibold text-indigo-400 transition hover:border-indigo-500/60 hover:bg-indigo-500/20 hover:text-indigo-300"
                                                >
                                                    View Results
                                                </button>
                                                <span v-else class="text-xs text-slate-600">—</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Pattern reference cards ────────────────────────────────── -->
                <div>
                    <h3 class="mb-4 text-xs font-bold uppercase tracking-widest text-slate-500">Laravel Patterns Used in This Feature</h3>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        <div v-for="p in [
                            { icon: '🏗️', name: 'Repository',  desc: 'ReportRepository', color: 'from-violet-500/10' },
                            { icon: '📦', name: 'DTO',          desc: 'ReportDTO',        color: 'from-blue-500/10'   },
                            { icon: '⚙️', name: 'Service',      desc: 'ReportService',    color: 'from-cyan-500/10'   },
                            { icon: '🔄', name: 'Job',          desc: 'GenerateReportJob',color: 'from-teal-500/10'   },
                            { icon: '📋', name: 'Queue',        desc: 'database driver',  color: 'from-emerald-500/10'},
                            { icon: '📡', name: 'Event',        desc: 'ReportGenerated',  color: 'from-amber-500/10'  },
                            { icon: '👂', name: 'Listener',     desc: 'HandleReportGenerated', color: 'from-orange-500/10'},
                            { icon: '💻', name: 'Command',      desc: 'report:daily',     color: 'from-red-500/10'    },
                            { icon: '🕐', name: 'Schedule',     desc: 'dailyAt midnight', color: 'from-pink-500/10'   },
                            { icon: '⚡', name: 'Reverb',       desc: 'WebSocket push',   color: 'from-indigo-500/10' },
                        ]" :key="p.name"
                             :class="['group rounded-xl border border-slate-700/40 bg-gradient-to-br p-4 hover:border-slate-600/60 transition-all duration-200', p.color, 'to-transparent']"
                        >
                            <div class="mb-2 text-2xl">{{ p.icon }}</div>
                            <p class="font-bold text-white text-sm">{{ p.name }}</p>
                            <p class="mt-0.5 text-xs font-mono text-slate-500 truncate">{{ p.desc }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>

    <!-- ── Report Result Modal ─────────────────────────────────────────────── -->
    <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/70 backdrop-blur-sm"
                @click="closeModal"
            ></div>
            <!-- Modal -->
            <div class="relative w-full max-w-lg overflow-hidden rounded-2xl border border-slate-700/50 bg-slate-900 shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-700/50 px-6 py-4">
                    <div>
                        <h3 class="font-bold text-white">{{ selectedReport?.title }}</h3>
                        <p class="text-xs text-slate-400">Generated {{ selectedReport?.generated_at }}</p>
                    </div>
                    <button @click="closeModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div v-if="selectedReport?.result" class="p-6 space-y-4">
                    <!-- KPIs -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-slate-800/60 border border-slate-700/40 p-4 text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Total Sales</p>
                            <p class="mt-1 text-2xl font-black text-emerald-400">${{ fmt(selectedReport.result.total_sales) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-800/60 border border-slate-700/40 p-4 text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Total Products</p>
                            <p class="mt-1 text-2xl font-black text-blue-400">{{ selectedReport.result.total_products }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-800/60 border border-slate-700/40 p-4 text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Avg. Price</p>
                            <p class="mt-1 text-2xl font-black text-violet-400">${{ fmt(selectedReport.result.average_order_value) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-800/60 border border-slate-700/40 p-4 text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Categories</p>
                            <p class="mt-1 text-2xl font-black text-amber-400">{{ selectedReport.result.top_categories?.length ?? 0 }}</p>
                        </div>
                    </div>
                    <!-- Top Categories -->
                    <div v-if="selectedReport.result.top_categories?.length" class="rounded-xl bg-slate-800/40 border border-slate-700/30 p-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Category Breakdown</p>
                        <div class="space-y-2">
                            <div v-for="cat in selectedReport.result.top_categories" :key="cat.category"
                                 class="flex items-center justify-between text-sm">
                                <span class="text-slate-300 font-medium">{{ cat.category }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-500">{{ cat.count }} items</span>
                                    <span class="font-bold text-emerald-400">${{ fmt(cat.revenue) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="border-t border-slate-700/50 px-6 py-4">
                    <button @click="closeModal"
                            class="w-full rounded-xl bg-slate-800 py-2.5 text-sm font-semibold text-slate-300 hover:bg-slate-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Slide-down animation for notification toast */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-1rem) scale(0.95);
}
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-0.5rem) scale(0.97);
}

/* Fade animation for modal */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
