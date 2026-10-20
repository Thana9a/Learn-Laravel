# FRONTEND RULES FOR THIS PROJECT (Vue 3 + Inertia.js + TypeScript + Laravel)

Follow this project's conventions for ALL frontend work. Do not invent a new
style — study existing code first, then match it.

## STEP 1 — LEARN THE STYLE FIRST
- Before writing any new page/component, open 2–3 existing similar pages in
  `resources/js/Pages/` and study their conventions:
  - `<script setup lang="ts">` structure (order of imports, props, state, functions)
  - Naming conventions for files, components, props, emits
  - How forms are built (Inertia `useForm` — see STEP 6)
  - How tables are built (custom `BaseTable`/`DataTable` — see STEP 8)
  - Tailwind class patterns already in use
  - Existing shared components/composables — reuse, don't rebuild
- Page-specific logic (fetch/derive state, computed values, polling, form
  wiring) is extracted into `resources/js/Composables/useXxx.ts`; files are
  kept presentational (see STEP 4 and the Composables catalog in STEP 8).
- If unsure which page is the best reference, ask before writing code.

## STEP 2 — PAGE STRUCTURE ("one page, one directory")
- Every page lives in its own folder: `resources/js/Pages/<Feature>/`
- Main entry file is always `Index.vue` (this is what Inertia renders).
- Anything only used by this page is a sibling file in the SAME folder,
  prefixed with the feature name. Real screens follow this pattern, e.g.:
  ```
  Pages/Orders/
    Index.vue
    OrderFilters.vue
    OrderStats.vue
    OrderTable.vue
    Show.vue
  Pages/Products/
    Index.vue
    ProductFilters.vue
    ProductForm.vue
    ProductStats.vue
    ProductTable.vue
    ProductRestockAlert.vue
  Pages/Reports/
    Index.vue
    ReportForm.vue
    ReportStats.vue
    ReportTable.vue
    ReportDetailModal.vue
    Show.vue
  ```
- `Index.vue` stays thin: compose child components + wire up shared state/props.
  Push detail/logic down into child components. Where several sibling files
  share logic, extract it to a `Composables/useFeature*.ts` (e.g. the Reports
  screen shares `useReportStats`, `useReportsTable`, `useReportForm`,
  `useReportResults`, `usePolling`).

## STEP 3 — REUSABLE COMPONENTS, ALWAYS
- Before creating any new UI piece, check `resources/js/Components/` first.
- Anything generic enough to be used on another page (buttons, modals,
  badges, table wrappers, pagination, etc.) MUST be extracted, not duplicated.
- Rule: if you're about to copy-paste a component into a second page, STOP —
  move it to `Components/` and import it in both places instead.
- Give every reusable component typed props/emits (`defineProps<T>()`,
  `defineEmits<T>()`) — no `any`.
- When the exact same markup shows up in 2+ files, extract it (e.g.
  `BaseListRow` was extracted from the duplicated report category rows).

## STEP 4 — FILE ORGANIZATION (composables / constants / schemas / types)
- Composables:  `resources/js/Composables/useXxx.ts`
                 Always prefix "use". Existing: `useDarkMode.ts`,
                 `usePermission.ts`, `useOrderCart.ts`, `usePolling.ts`,
                 `useReportStats.ts`, `useReportsTable.ts`, `useReportForm.ts`,
                 `useReportResults.ts`.
- Constants:    `resources/js/Constants/<feature>.constant.ts`
                 e.g. `darkMode.constant.ts`, `config/menu.ts`
- Schemas:      `resources/js/Schemas/<feature>.schema.ts`
                 (Zod is used here, e.g. `darkMode.schema.ts`)
- Types:        `resources/js/Types/<feature>.type.ts`
                 e.g. `darkMode.type.ts`, `order.type.ts`, `pos.type.ts`,
                 `product.type.ts`, `report.type.ts`
- If a composable/constant/schema/type is used ONLY inside one page, it's OK
  to keep it local inside that page's folder — but the moment it's needed in
  a 2nd page, move it to the shared folder above immediately.
- Always check these folders first before creating a new file — don't
  duplicate something that already exists.

## STEP 5 — LOCALIZATION (i18n / TypeScript Translations)
- All client-side text and UI labels MUST use the TypeScript translation files
  in `resources/js/lang/`.
- Structure (typed, `as const`, merged through the index):
  ```
  resources/js/lang/
    en/
      common.ts  auth.ts  dashboard.ts  discount.ts  menu.ts
      order.ts   payment.ts  product.ts  report.ts
    km/          (same module set as en/)
    en.ts        ← merges all en/* into one typed object
    km.ts        ← merges all km/* into one typed object
  ```
- **How translations are consumed:** import the merged typed object directly
  (NOT `vue-i18n`'s `$t`/`useI18n`):
  ```ts
  import en from '@/lang/en';
  const t = en.report;   // or en.common / en.product / etc.
  // template: {{ t.dashboard.results.sales }}
  ```
- **Rules for Translations:**
  - Never hardcode raw user-facing strings directly in components without
    matching keys in `resources/js/lang/`.
  - Use strongly typed structures (`as const`) for autocomplete and
    compile-time type safety.
  - Maintain key symmetry between `en/` and `km/`.
  - Export merged translation objects via `en.ts` and `km.ts`.

## STEP 6 — CODE STYLE
- Composition API with `<script setup lang="ts">` only.
- TypeScript types/interfaces for all props, emits, and form data — no `any`.
- **Forms are built with Inertia `useForm`** (`@inertiajs/vue3`), NOT
  vee-validate. Example (see `Pages/Products/ProductForm.vue`):
  ```ts
  import { useForm } from '@inertiajs/vue3';
  const form = useForm({ name: '', price: '' });
  form.post(route('products.store'), {
    onSuccess: () => emit('created'),
  });
  // template: <BaseInput v-model="form.name" :error="form.errors.name" />
  ```
  Reports picks this up through the `useReportForm` composable, which returns
  a typed `form` plus a `submitGenerate(callbacks)` helper.
- Keep Tailwind classes consistent with what's already used — don't
  introduce a new styling approach (see DESIGN_SYSTEM.md tokens).

### Semantic dark-mode tokens
For light/dark support, reuse the semantic tokens declared as CSS variables in
`resources/css/app.css` (`:root` = light, `.dark` = dark) and wired into
`tailwind.config.js` `theme.extend.colors`. Use ONE token class instead of
writing paired `light dark:` utility classes (e.g. `text-neutral-500
dark:text-neutral-400` → `text-muted-foreground`). Mapping (pixel-identical to the
original pairs):
| Token | = light / dark | Use for |
|---|---|---|
| `bg-surface` | `bg-white` / `bg-neutral-800` | card / panel background |
| `bg-surface-muted` | `bg-neutral-50` / `bg-neutral-900` | inset / muted background |
| `text-foreground` | `text-neutral-900` / `text-neutral-100` | primary text / headings |
| `text-body` | `text-neutral-700` / `text-neutral-300` | body text |
| `text-muted-foreground` | `text-neutral-500` / `text-neutral-400` | labels / helper text |
| `bg-muted` | `bg-neutral-100` / `bg-neutral-800` | subtle fills (shadcn `bg-muted`) |
| `border-border` | `border-neutral-200` / `border-neutral-700` | borders / dividers |

Rules: status colors (success/warning/danger/info), translucent surfaces
(`bg-*-/50`), one-off light/dark pairs, `placeholder-*`, and hover pseudo-state
classes (`dark:hover:bg-...`) are NOT tokenized — leave those as-is. Colors
that differ from the mapping above are NOT safe to collapse into a token.

- Before finishing, run the typecheck: `npx vue-tsc --noEmit -p tsconfig.json`
  (should exit 0).

## STEP 7 — CONSISTENCY CHECK BEFORE FINISHING
- Compare your output against the reference pages from Step 1. If naming,
  structure, or style doesn't match, fix it before showing me.
- Briefly tell me which existing page(s) you used as reference, so I can verify.

---

## STEP 8 — REUSABLE COMPONENT CATALOG (`resources/js/Components/`)

Before building any UI, check this catalog first and reuse — don't rebuild.
All design-system components use `defineProps<T>()` / `defineEmits<T>()` typed
API (no `any`) and conform to the `neutral-*` / `primary-*` / `success-*` /
`warning-*` / `danger-*` / `info-*` token palette in DESIGN_SYSTEM.md.

### Design-system components (legacy `Base*` — use only in not-yet-migrated screens; new UI uses `Components/ui/*` below)

| Component | Purpose |
|---|---|
| `BaseButton` | Buttons + links-as-buttons. `variant: primary\|secondary\|danger\|ghost`, `size: sm\|md\|lg\|icon`, `href`, `icon` (lucide), `iconPosition`, `loading`, `fullWidth`. Renders `<Link>` when `href`, else `<button>` (emits `click`). |
| `BaseCard` | Card container. `as`, `padding: none\|sm\|md\|lg`, `flush` (shorthand for `padding="none"` + `overflow-hidden`, ideal for tables), `elevated`. Base rule: `bg-surface border border-border p-4 shadow-sm`. |
| `Card` | Convenience wrapper around `BaseCard` with structured header + footer. Props `title`, `subtitle`, `icon`, `iconColorClass`, plus `flush`/`padding`/`elevated`. Slots: default body, `actions` (header-right), `footer`. Renders `CardHeader` (when title/icon/actions present) and `CardFooter` (when `#footer` given) automatically. |
| `CardHeader` | Card header block (icon + title, optional subtitle). Props `title`, `subtitle`, `icon`, `iconColorClass` (default `text-primary-500`); `actions` slot is right-aligned. Matches `mb-4 flex items-center gap-2`. |
| `CardContent` | Card main body section, between header and footer. Prop `divider` adds a top border (`border-t ... pt-4`). Default slot holds body content; `class` merges onto the root. |
| `CardFooter` | Card footer with top divider border (`border-t border-border pt-4`). Just a `<footer>` with default slot; add `class` like `mt-4` at the call site. |
| `BaseText` | Typography. `size: xs\|sm\|base\|lg\|xl\|2xl`, `variant: default\|muted\|error\|success\|warning\|info`, `weight`, `as` (p/span/div/h1-h4/label), `inline`, `truncate`, `align`, `text`/default slot. |
| `BaseBadge` | Semantic status pill with dot. `variant` (explicit) **or** `status` (auto-resolves: paid/completed/active→success, pending/low→warning, cancelled/failed/void/out→danger, processing/queued→info, else neutral), `dot`, `pulse`, `label`/default slot. |
| `BaseInput` | Text input. `modelValue`, `label`, `type`, `placeholder`, `error`, `hint`, `icon`, `required`, `min/max/step`, `disabled`. h-12. Emits `update:modelValue`, `change`, `keydown`. Exposes `focus()`. |
| `BaseSelect` | Dropdown. `modelValue`, `label`, `options: string[]\|number[]\|{label,value,disabled?}[]`, `placeholder`, `error`, `hint`, `required`, `disabled`. Default slot for custom `<option>`. Emits `update:modelValue`, `change`. |
| `BaseTextarea` | Multiline. `modelValue`, `label`, `rows`, `placeholder`, `error`, `hint`, `required`, `disabled`. Emits `update:modelValue`. |
| `BaseCheckbox` | Row-style toggle with optional icon. `modelValue: boolean`, `label`, `icon`. Emits `update:modelValue`. |
| `BaseModal` | Modal overlay (Teleport). `show`, `title`, `subtitle`, `maxWidth: sm\|md\|lg\|xl\|2xl`, `closeable`. Slots: default body, `header`, `footer`. Emits `close`. Esc + backdrop close, body-scroll lock. |
| `BasePagination` | Table footer pagination. Props `meta` (Laravel paginator) **or** raw `prevUrl/nextUrl/currentPage/from/to/total`, plus `prevLabel/nextLabel/showingLabel`. Default slot overrides center "Showing X-Y of Z". |
| `InfoList` | **Generic** (`<script setup generic="T extends { id: string\|number }">`) list container for labelled-row cards. Props `items: T[]`, `emptyMessage`. Renders `space-y-3` list of items or the empty state. Slots: `#item` scoped `{ item: T, index }` (full row markup — caller owns styling), `#empty` (custom empty state; defaults to `emptyMessage`). Used by `ProductRestockAlert`; each caller supplies its own row + empty markup via typed slots (no boolean prop explosion). |
| `BaseListRow` | Label (+ optional sublabel) on the left, value on the right. Props `label`, `sublabel?`, `value?: string\|number`, `mono?: boolean` (font-mono for currency/numeric values). Renders `flex items-center justify-between gap-4 px-4 py-3 text-sm`. Used for report category breakdowns (Reports Show + ReportDetailModal). |
| `StatCard` | KPI stat card. `label`, `value`, `hint`, `trend`, `icon`, `iconColorClass` (default `text-primary-500`). Icon top-right, label above value, hint/trend below. |
| `StatMiniCard` | Compact KPI stat block (muted `xs` label + `xl` value inside `BaseCard padding="sm"` on `bg-surface-muted`). Props `label`, `value: string\|number`, `mono?: boolean`. Used in report KPI grids. |

### Tables (custom — upgrade to TanStack Table (`@tanstack/vue-table`) is the agreed direction; migrate per-screen)

Two table approaches exist. Match whichever the feature already uses:

- **`BaseTable`** — low-level table shell with sticky header + `columns` array
  (`TableColumn: { key, label, align?, isMono?, class? }`). Slots: `header`,
  default body, `empty`. Used with `BaseTableRow` + `BaseTableCell` for
  explicit row/cell control (see `Pages/Orders/OrderTable.vue`,
  `Pages/Reports/ReportTable.vue`).
  - `BaseTableRow` — body row (`h-12 border-b ... last:border-0`); `clickable?`
    adds hover bg + cursor-pointer.
  - `BaseTableCell` — body cell. `align: left\|center\|right`, `muted?`,
    `size: xs\|sm\|base`. Padding `px-4 py-3`.
- **`DataTable`** — config-driven convenience wrapper (see below). Used by
  `Pages/Products/ProductTable.vue`.

### `DataTable` usage (config-driven tables)

```ts
import DataTable, { type DataTableColumn } from '@/Components/DataTable.vue';

const columns: DataTableColumn[] = [
  { key: 'order_number', label: t.order_id, format: 'text' },
  { key: 'created_at',   label: t.time,     format: 'time' },        // HH:MM local
  { key: 'total',        label: t.total,    align: 'right', format: 'currency' }, // `$X.XX` font-mono
  { key: 'status',       label: t.status,   align: 'center', format: 'badge' },   // <BaseBadge :status>
];

<DataTable
  :columns="columns"
  :rows="rows"             <!-- Record<string, unknown>[], keyed by column `key` -->
  row-key="id"             <!-- default 'id' -->
  :empty-message="t.empty"
>
  <!-- Override any cell with a scoped slot; falls back to `format` otherwise -->
  <template #cell-order_number="{ row }">
    <Link :href="route('orders.show', (row as any).id)">{{ (row as any).order_number }}</Link>
  </template>
</DataTable>
```

- Column fields: `key`, `label`, `align? ('left'|'center'|'right')`, `format? ('text'|'currency'|'date'|'time'|'badge')`.
- Slot name is `#cell-<columnKey>`, receives `{ row, value }`.
- Built-in formats: `currency` → `$X.XX` + `font-mono font-medium text-foreground`; `time` → `toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' })`; `date` → `toLocaleDateString()`; `badge` → `<BaseBadge :status>`; `text` → raw value.
- Wraps `BaseCard :flush="true"` internally; empty state renders a single `colspan` row using `emptyMessage`.

### Composables (`resources/js/Composables/`)

Existing composables follow `export function useXxx()` returning a plain
object of `ref`/`computed`/functions (`computed<T>()` with explicit generics,
types imported from `@/Types/`):

- `useDarkMode` / `usePermission` — app-level state.
- `useOrderCart` — POS cart state.
- `usePolling(callback, intervalMs, enabled)` — generic lifecycle-managed
  polling interval (used by Reports Index, gated on `hasProcessing`).
- `useReportStats(stats)` → `{ readyRate, queueCount }`.
- `useReportsTable()` → `{ columns: TableColumn[], canView(report) }`.
- `useReportForm()` → `{ form, reportTypes, submitGenerate(callbacks) }`.
- `useReportResults()` → `{ fmt, money }` (shared number/currency formatters).

### Page / layout / dashboard widgets

- `PageHeader` | page title block. `eyebrow`, `title`, `subtitle`, `actions` slot (right-aligned).

### Legacy Breeze components (default Laravel starter — DO NOT use for new UI)

These still exist (used by Auth/Profile pages) but use the OLD gray/indigo
Breeze token palette that does NOT match this repo's design system. Do NOT use
them in new screens — reach for the `Base*` equivalents instead:
`TextInput` (→`BaseInput`), `Checkbox` (→`BaseCheckbox`),
`InputError` (→BaseInput `error`), `InputLabel` (→BaseInput `label`),
`PrimaryButton`/`SecondaryButton`/`DangerButton` (→`BaseButton`), `Modal`
(→`BaseModal`), `Dropdown`/`DropdownLink` (→ keep only if you also port them to
the design system), `NavLink`/`ResponsiveNavLink` (layout nav).

### Other / layout & POS

- `Sidebar` — left nav (reads `@/config/menu` + `usePermission`). Non-generic app shell.
- `ApplicationLogo` — brand/logo mark.
- `Pos/*` (POS screen building blocks):
  - `CategoryTabs` — `categories[], activeCategory`, `update:activeCategory` emit.
  - `ProductCard` — `product`, `cartQuantity?`, emits `add(product)`.
  - `MenuGrid` — grid of `ProductCard`s.
  - `OrderCart` — cart panel; `OrderCartItem` — single cart row.
  - `QuantitySelector` — `+`/`-` stepper.

### Rules for adding new shared components
1. Before creating, grep `resources/js/Components/` for something that already fits.
2. Name with `Base` prefix for primitives (inputs, cards, modals), or a
   descriptive noun (StatCard, DataTable, PageHeader) for composition widgets.
3. Keep the exact existing Tailwind classes / dark-mode variants; never invent
   new tokens or a UI library.
4. Typed `defineProps<T>()` + `defineEmits<T>()`; expose `interface`/`type`
   exports (e.g. `DataTableColumn`, `TableColumn`) so callers get autocomplete.
5. If a component must support custom per-instance rendering, expose a scoped
   slot (`#cell-<key>`, `<slot name="actions">`, etc.) with a typed context
   object — don't bolt on dozens of boolean props.

## shadcn-vue primitives (migration in progress — new UI MUST use these)

The hand-rolled `Base*` set is being replaced by shadcn-vue components,
feature-by-feature. **Dashboard.vue is the reference implementation.**

- Primitives live in `resources/js/Components/ui/<name>/`. Add more with:
  `npx shadcn-vue@latest add <name>` — registry style is `new-york`
  (Tailwind-v3-compatible); `components.json` is configured, aliases point at
  `@/Components/ui`.
- Available now: `badge`, `button`, `card`, `checkbox`, `dialog`, `input`,
  `label`, `pagination`, `select`, `table`, `textarea`.
- `cn()` helper: `resources/js/lib/utils.ts` (clsx + tailwind-merge).
- Status → badge mapping: `resources/js/lib/status.ts`
  (`statusBadgeClass`, `statusDotClass`, `statusLabel`) — use with `<Badge variant="outline">`.
- Icons: keep `lucide-vue-next`. Do NOT add `@lucide/vue` (the CLI may try; remove it).
- Theme: shadcn HSL vars in `resources/css/app.css` are mapped 1:1 onto the
  existing palette (`--primary` = `#f97316`), dark mode stays class-based (`.dark`).
- This registry ships UI components only — no “blocks” (dashboard-01, login-01,
  sidebar-07, …). Compose screens from `ui/*` primitives + the existing shell.
- Composed screens use `Card`/`CardHeader`/`CardContent` + `Table` primitives;
  put shared status/format logic in `lib/`, page-specific logic in `Composables/`.

**Migration status**
- Done: `Pages/Dashboard.vue` (ui/card, ui/button, ui/table, ui/badge,
  `lib/status`; hardcoded headers moved into `lang/en|km/dashboard.ts`).
- Removed permanently: `DashboardHeader`, `QuickActionCard`, `RevenueChartCard`,
  `StaffPerformanceCard`, `RecentOrdersTable`, `Card.vue` (wrapper),
  `BaseResizablePanel.vue` + `useResizablePanel.ts`, `NavLink`, `ResponsiveNavLink`.
- Removed with replacement: `text-muted` → `text-muted-foreground` (59 usages).
- Pending: Products, Orders, Reports, POS, Auth/Profile screens still use the
  legacy `Base*` set — port them next, then delete each `Base*` once its last
  usage is gone (do not add new usages of `Base*` in the meantime).
