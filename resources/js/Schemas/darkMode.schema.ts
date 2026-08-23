import { z } from 'zod';

/** Zod schema to validate a ThemeMode value */
export const themeModeSchema = z.enum(['light', 'dark', 'system']);

/** Zod schema to validate a stored dark mode preference object */
export const darkModePreferenceSchema = z.object({
  mode: themeModeSchema,
});

export type ThemeModeSchema = z.infer<typeof themeModeSchema>;
export type DarkModePreferenceSchema = z.infer<typeof darkModePreferenceSchema>;
