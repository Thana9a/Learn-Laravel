import { onMounted, onUnmounted } from 'vue';

/**
 * usePolling — run a callback on an interval while a condition is met.
 *
 * Generic lifecycle-managed interval used (e.g.) by the Reports index page to
 * auto-refresh while jobs are still processing. Follows the senior convention:
 * `export function useXxx()` invoked from within `setup`.
 */
export function usePolling(callback: () => void, intervalMs: number, enabled: () => boolean) {
  let timer: ReturnType<typeof setInterval> | null = null;

  onMounted(() => {
    timer = setInterval(() => {
      if (enabled()) {
        callback();
      }
    }, intervalMs);
  });

  onUnmounted(() => {
    if (timer) {
      clearInterval(timer);
    }
  });
}
