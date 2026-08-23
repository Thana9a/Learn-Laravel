# FRONTEND RULES FOR THIS PROJECT (Vue 3 + Inertia.js + TypeScript + Laravel)

Follow this project's conventions for ALL frontend work. Do not invent a new
style — study existing code first, then match it.

## STEP 1 — LEARN THE STYLE FIRST
- Before writing any new page/component, open 2–3 existing similar pages in
  resources/js/Pages/ and study their conventions:
  - <script setup lang="ts"> structure (order of imports, props, state, functions)
  - Naming conventions for files, components, props, emits
  - How forms are built (vee-validate + Zod)
  - How tables are built (TanStack Table)
  - Tailwind class patterns already in use
  - Existing shared components/composables — reuse, don't rebuild
- If unsure which page is the best reference, ask before writing code.

## STEP 2 — PAGE STRUCTURE ("one page, one directory")
- Every page lives in its own folder: resources/js/Pages/<Feature>/
- Main entry file is always Index.vue (this is what Inertia renders).
- Anything only used by this page (form, table, filters, modal) is a sibling
  file in the SAME folder, prefixed with the feature name:
    Pages/BlacklistIp/
      Index.vue
      BlacklistIpForm.vue
      BlacklistIpTable.vue
- Index.vue stays thin: compose child components + wire up state/props.
  Push detail/logic down into the child components.

## STEP 3 — REUSABLE COMPONENTS, ALWAYS
- Before creating any new UI piece, check resources/js/Components/ first.
- Anything generic enough to be used on another page (buttons, modals,
  badges, table wrappers, pagination, etc.) MUST be extracted, not duplicated.
- Rule: if you're about to copy-paste a component into a second page, STOP —
  move it to Components/ and import it in both places instead.
- Give every reusable component typed props/emits (defineProps<T>(),
  defineEmits<T>()) — no `any`.

## STEP 4 — FILE ORGANIZATION (composables / constants / schemas / types)
- Composables:  resources/js/Composables/useXxx.ts
                 Always prefix "use" (e.g. useDarkMode.ts, usePagination.ts)
- Constants:    resources/js/Constants/<feature>.constant.ts
                 (e.g. darkMode.constant.ts)
- Schemas:      resources/js/Schemas/<feature>.schema.ts
                 Zod / vee-validate schemas (e.g. darkMode.schema.ts)
- Types:        resources/js/Types/<feature>.type.ts
                 Interfaces/types (e.g. darkMode.type.ts)
- If a composable/constant/schema/type is used ONLY inside one page, it's OK
  to keep it local inside that page's folder — but the moment it's needed in
  a 2nd page, move it to the shared folder above immediately.
- Always check these folders first before creating a new file — don't
  duplicate something that already exists.

## STEP 5 — LOCALIZATION (i18n / TypeScript Translations)
- All client-side text and UI labels MUST use the TypeScript translation files in `resources/js/lang/`.
- Structure:
  ```text
  resources/js/lang/
    en/
      common.ts
      product.ts
      order.ts
      discount.ts
      payment.ts
      auth.ts
    km/
      common.ts
      product.ts
      order.ts
      discount.ts
      payment.ts
      auth.ts
    en.ts       ← merges all en/* into one typed object
    km.ts       ← merges all km/* into one typed object
  ```
- **Rules for Translations:**
  - Never hardcode raw user-facing strings directly in components without matching keys in `resources/js/lang/`.
  - Use strongly typed structures (`as const`) for autocomplete and compile-time type safety.
  - Maintain key symmetry between `en/` and `km/`.
  - Export merged translation objects via `en.ts` and `km.ts`.

## STEP 6 — CODE STYLE
- Composition API with <script setup lang="ts"> only.
- TypeScript types/interfaces for all props, emits, and form data — no `any`.
- Match existing patterns already in the repo (Spatie LaravelData DTOs on
  backend, TanStack Table for tables, vee-validate + Zod for forms).
- Keep Tailwind classes consistent with what's already used — don't
  introduce a new styling approach.

## STEP 7 — CONSISTENCY CHECK BEFORE FINISHING
- Compare your output against the reference pages from Step 1. If naming,
  structure, or style doesn't match, fix it before showing me.
- Briefly tell me which existing page(s) you used as reference, so I can verify.