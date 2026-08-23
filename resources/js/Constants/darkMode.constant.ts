import type { ThemeMode } from '@/Types/darkMode.type';

/** localStorage key for persisting theme preference */
export const DARK_MODE_STORAGE_KEY = 'theme-mode' as const;

/** Default theme mode when no preference is stored */
export const DARK_MODE_DEFAULT: ThemeMode = 'system';

/** CSS class applied to the <html> element for dark mode */
export const DARK_MODE_CLASS = 'dark' as const;

/** Media query for detecting system dark mode preference */
export const DARK_MODE_MEDIA_QUERY = '(prefers-color-scheme: dark)' as const;

/** Available theme modes for UI selectors */
export const THEME_MODES: readonly ThemeMode[] = ['light', 'dark', 'system'] as const;
