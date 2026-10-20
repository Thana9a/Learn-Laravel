import { ref, watch } from 'vue';
import { SIDEBAR_COLLAPSED_STORAGE_KEY } from '@/Constants/sidebar.constant';

/**
 * Read the persisted sidebar preference.
 * Falls back to expanded when storage is unavailable or empty.
 */
function readStoredCollapsed(): boolean {
  if (typeof window === 'undefined') return false;

  try {
    return localStorage.getItem(SIDEBAR_COLLAPSED_STORAGE_KEY) === 'true';
  } catch {
    return false;
  }
}

// Shared singleton state so every consumer sees the same sidebar state.
const isCollapsed = ref<boolean>(readStoredCollapsed());

watch(isCollapsed, (collapsed) => {
  try {
    localStorage.setItem(SIDEBAR_COLLAPSED_STORAGE_KEY, String(collapsed));
  } catch {
    // Storage unavailable — ignore, state still works for this session.
  }
});

export function useSidebar() {
  function toggle(): void {
    isCollapsed.value = !isCollapsed.value;
  }

  function collapse(): void {
    isCollapsed.value = true;
  }

  function expand(): void {
    isCollapsed.value = false;
  }

  return {
    isCollapsed,
    toggle,
    collapse,
    expand,
  };
}
