FRONTEND RULES FOR THIS PROJECT (Vue 3 + Inertia.js + TypeScript + Laravel)

Before writing any code, first study this codebase's existing patterns and follow
them exactly. Do NOT invent a new style — match what the senior dev already wrote.

STEP 1 — LEARN THE STYLE FIRST
   - Open 2–3 existing pages inside resources/js/Pages/ (pick ones similar to what
     I'm asking for now, e.g. a CRUD page with a form + table).
   - Study and copy their conventions:
     - How <script setup lang="ts"> is structured (order of imports, props, state,
       functions)
     - Naming conventions for files, components, props, emits
     - How forms are built (vee-validate? Zod? useForm from Inertia?)
     - How tables are built (TanStack Table? plain v-for?)
     - Tailwind class patterns / spacing / component styling already used
     - Any shared components already in resources/js/Components/ — reuse them,
       don't rebuild them
   - If you're unsure which existing page is the best reference, ask me first
     before writing code.

STEP 2 — PAGE STRUCTURE ("one page, one directory")
   - Every page lives in its own folder: resources/js/Pages/<Feature>/
   - Main entry file is always Index.vue (this is what Inertia renders).
   - Anything only used by this page (form, table, filters, modal) is a sibling file
     in the SAME folder, prefixed with the feature name:
       Pages/BlacklistIp/
         Index.vue
         BlacklistIpForm.vue
         BlacklistIpTable.vue
   - Index.vue stays thin: just composes child components + wires up state/props.
     Push detail/logic into the child components.

STEP 3 — REUSABLE COMPONENTS, ALWAYS
   - Before creating any new UI piece, check resources/js/Components/ first — if
     something similar already exists, use/extend it instead of duplicating.
   - Anything generic enough to be used on another page (buttons, modals, badges,
     table wrappers, pagination, etc.) MUST be extracted to:
       resources/js/Components/    → shared UI
       resources/js/Composables/   → shared logic (usePagination, useDebounce, etc.)
       resources/js/Types/         → shared TS types/interfaces
   - Rule: if you're about to copy-paste a component into a second page, STOP —
     move it to Components/ instead and import it in both places.
   - Give every reusable component typed props/emits (defineProps<T>(),
     defineEmits<T>()), no `any`.

STEP 4 — CONSISTENCY CHECK BEFORE FINISHING
   - After writing the code, compare it against the reference pages from Step 1.
     If naming, structure, or style doesn't match, fix it before showing me.
   - Tell me briefly which existing page(s) you used as reference, so I can verify.

