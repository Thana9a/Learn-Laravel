import { onBeforeUnmount, ref } from 'vue';
import type { Ref } from 'vue';

/**
 * useResizablePanel — persistent, clamped left-panel width for a split panel.
 *
 * Drives the width of a left panel (percentage) while a divider is dragged.
 * Follows the senior convention: `export function useXxx()` returning a plain
 * object of `ref`s / functions from within `setup`. Used by
 * `BaseResizablePanel.vue`.
 */

export interface ResizablePanelOptions {
  /** Initial left-panel width as a percentage (0-100). Default 50. */
  initialLeftWidth?: number;
  /** Minimum left-panel width as a percentage (0-100). Default 20. */
  minLeftWidth?: number;
  /** Maximum left-panel width as a percentage (0-100). Default 80. */
  maxLeftWidth?: number;
  /** Optional localStorage key to persist the chosen width across visits. */
  storageKey?: string;
}

export function useResizablePanel(options: ResizablePanelOptions = {}) {
  const { initialLeftWidth = 50, minLeftWidth = 20, maxLeftWidth = 80, storageKey } = options;

  const clamp = (value: number): number => Math.min(maxLeftWidth, Math.max(minLeftWidth, value));

  const leftWidth: Ref<number> = ref<number>(clamp(readStoredWidth() ?? initialLeftWidth));
  const isDragging: Ref<boolean> = ref<boolean>(false);

  let containerEl: HTMLElement | null = null;

  function readStoredWidth(): number | null {
    if (!storageKey) return null;
    try {
      const raw = localStorage.getItem(storageKey);
      if (raw === null) return null;
      const parsed = Number(raw);
      return Number.isFinite(parsed) ? parsed : null;
    } catch {
      return null;
    }
  }

  function persistWidth(width: number): void {
    if (!storageKey) return;
    try {
      localStorage.setItem(storageKey, String(Math.round(width)));
    } catch {
      /* ignore storage errors (private mode / quota) */
    }
  }

  function onMouseMove(event: MouseEvent): void {
    if (!containerEl) return;
    const rect = containerEl.getBoundingClientRect();
    if (rect.width === 0) return;
    const ratio = ((event.clientX - rect.left) / rect.width) * 100;
    leftWidth.value = clamp(ratio);
    persistWidth(leftWidth.value);
  }

  function stopDrag(): void {
    isDragging.value = false;
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', stopDrag);
    window.removeEventListener('mouseleave', stopDrag);
  }

  /**
   * Begin a drag session. Wire handlers onto `el` in a `@mousedown` handler —
   * the element passed should be the split-panel container (needed to compute
   * the width ratio). Listeners are removed on mouse-up / mouse-leave and
   * guaranteed removed on component unmount.
   */
  function startDrag(el: HTMLElement): void {
    containerEl = el;
    isDragging.value = true;
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', stopDrag);
    window.addEventListener('mouseleave', stopDrag);
  }

  onBeforeUnmount(() => {
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', stopDrag);
    window.removeEventListener('mouseleave', stopDrag);
  });

  return {
    leftWidth,
    isDragging,
    startDrag,
  };
}
