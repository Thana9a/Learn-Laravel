import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import type { ThemeMode, UseDarkModeReturn } from '@/Types/darkMode.type';
import {
  DARK_MODE_STORAGE_KEY,
  DARK_MODE_DEFAULT,
  DARK_MODE_CLASS,
  DARK_MODE_MEDIA_QUERY,
} from '@/Constants/darkMode.constant';
import { themeModeSchema } from '@/Schemas/darkMode.schema';

/**
 * Safely read the stored theme mode from localStorage.
 * Falls back to DARK_MODE_DEFAULT if the value is missing or invalid.
 */
function readStoredMode(): ThemeMode {
  try {
    const raw = localStorage.getItem(DARK_MODE_STORAGE_KEY);
    if (!raw) return DARK_MODE_DEFAULT;

    const parsed = themeModeSchema.safeParse(raw);
    return parsed.success ? parsed.data : DARK_MODE_DEFAULT;
  } catch {
    return DARK_MODE_DEFAULT;
  }
}

/**
 * Check whether the operating system prefers dark mode.
 */
function systemPrefersDark(): boolean {
  if (typeof window === 'undefined') return false;
  return window.matchMedia(DARK_MODE_MEDIA_QUERY).matches;
}

/**
 * Apply or remove the dark class on the <html> element.
 */
function applyClass(isDark: boolean): void {
  if (typeof document === 'undefined') return;
  document.documentElement.classList.toggle(DARK_MODE_CLASS, isDark);
}

// ── Shared singleton state (same pattern as useOrderCart) ──────────────
const mode = ref<ThemeMode>(readStoredMode());

export function useDarkMode(): UseDarkModeReturn {
  let mediaQuery: MediaQueryList | null = null;
  let mediaHandler: ((e: MediaQueryListEvent) => void) | null = null;

  const isDark = computed<boolean>(() => {
    if (mode.value === 'system') return systemPrefersDark();
    return mode.value === 'dark';
  });

  function setMode(newMode: ThemeMode): void {
    mode.value = newMode;
  }

  function toggle(): void {
    mode.value = isDark.value ? 'light' : 'dark';
  }

  // Persist & apply whenever the resolved dark state changes
  watch(
    isDark,
    (dark) => {
      applyClass(dark);
    },
    { immediate: true },
  );

  // Persist the raw mode preference
  watch(mode, (m) => {
    try {
      localStorage.setItem(DARK_MODE_STORAGE_KEY, m);
    } catch {
      // SSR or storage full — ignore
    }
  });

  // Listen for OS-level theme changes when in 'system' mode
  onMounted(() => {
    if (typeof window === 'undefined') return;

    mediaQuery = window.matchMedia(DARK_MODE_MEDIA_QUERY);
    mediaHandler = () => {
      if (mode.value === 'system') {
        applyClass(systemPrefersDark());
      }
    };
    mediaQuery.addEventListener('change', mediaHandler);

    // Ensure class is correct on first mount
    applyClass(isDark.value);
  });

  onUnmounted(() => {
    if (mediaQuery && mediaHandler) {
      mediaQuery.removeEventListener('change', mediaHandler);
    }
  });

  return {
    mode,
    isDark,
    setMode,
    toggle,
  };
}
