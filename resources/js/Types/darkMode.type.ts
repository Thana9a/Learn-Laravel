import type { ComputedRef, Ref } from 'vue';

export type ThemeMode = 'light' | 'dark' | 'system';

export interface DarkModeState {
  /** The user's chosen preference */
  mode: ThemeMode;
  /** The resolved (effective) theme after evaluating 'system' */
  isDark: boolean;
}

export interface UseDarkModeReturn {
  /** The current theme mode preference */
  mode: Ref<ThemeMode>;
  /** Whether dark mode is currently active (resolved) */
  isDark: ComputedRef<boolean>;
  /** Set the theme mode */
  setMode: (newMode: ThemeMode) => void;
  /** Toggle between light and dark (ignores system) */
  toggle: () => void;
}
