/**
 * Shared status → visual mapping for `<Badge>` in `@/Components/ui/badge`.
 *
 * Mirrors the semantics previously centralised in BaseBadge so orders,
 * products, and reports keep the same colour language once they migrate.
 */

export type StatusVariant = 'success' | 'warning' | 'danger' | 'info' | 'neutral';

/** Map a raw status string to a semantic variant. */
export function statusVariant(status?: string | null): StatusVariant {
    if (!status) return 'neutral';

    switch (status.toLowerCase().trim()) {
        case 'done':
        case 'paid':
        case 'completed':
        case 'ready':
        case 'in_stock':
        case 'active':
        case 'success':
            return 'success';

        case 'pending':
        case 'preparing':
        case 'low':
        case 'low_stock':
        case 'warning':
        case 'action_needed':
            return 'warning';

        case 'failed':
        case 'cancelled':
        case 'canceled':
        case 'void':
        case 'out':
        case 'out_of_stock':
        case 'danger':
        case 'error':
            return 'danger';

        case 'processing':
        case 'queued':
        case 'generating':
        case 'info':
            return 'info';

        default:
            return 'neutral';
    }
}

const pillClasses: Record<StatusVariant, string> = {
    success:
        'border-transparent bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500',
    warning:
        'border-transparent bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-500',
    danger:
        'border-transparent bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-500',
    info: 'border-transparent bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-500',
    neutral:
        'border-transparent bg-neutral-100 text-body dark:bg-neutral-800 dark:text-neutral-300',
};

const dotClasses: Record<StatusVariant, string> = {
    success: 'bg-success-500',
    warning: 'bg-warning-500',
    danger: 'bg-danger-500',
    info: 'bg-info-500',
    neutral: 'bg-neutral-500',
};

/** Pill classes for a status badge (combine with `rounded-full`). */
export function statusBadgeClass(status?: string | null): string {
    return pillClasses[statusVariant(status)];
}

/** Dot colour for a status badge. */
export function statusDotClass(status?: string | null): string {
    return dotClasses[statusVariant(status)];
}

/** Human-readable status label (`in_stock` → `in stock`). */
export function statusLabel(status?: string | null): string {
    return (status ?? '').replace(/_/g, ' ');
}
