# DESIGN SYSTEM RULES — Coffee Shop POS Staff Dashboard (Vue 3 + Tailwind)

Whenever you create, style, or edit ANY component (button, table, card, badge,
form, modal, etc.), follow this design system exactly. Do NOT invent new colors,
spacing, or button styles — always check this list first.

## STEP 0 — CHECK BEFORE CREATING
- Before building any new UI piece, check if tailwind.config.js already has the
  color tokens below. If not, add them first.
- Check resources/js/Components/ for an existing Base component (BaseButton,
  BaseBadge, BaseTable, BaseCard) before creating a new one. Extend, don't duplicate.

## COLOR TOKENS (add to tailwind.config.js if missing)
```js
colors: {
  primary: { 50: '#fff7ed', 100: '#ffedd5', 500: '#f97316', 600: '#ea580c', 700: '#c2410c' },
  neutral: { 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 500: '#71717a', 700: '#3f3f46', 900: '#18181b' },
  success: { 50: '#f0fdf4', 500: '#22c55e', 600: '#16a34a' },
  warning: { 50: '#fffbeb', 500: '#f59e0b', 600: '#d97706' },
  danger:  { 50: '#fef2f2', 500: '#ef4444', 600: '#dc2626' },
  info:    { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb' },
}
```
- primary = brand/main actions only
- success/warning/danger/info = STATUS ONLY, never decoration
- neutral = all text, borders, backgrounds (never pure black #000 or white #fff)
- Never use arbitrary hex values inline — always use these tokens.

## BUTTON RULES
- Only ONE primary button per screen/section (e.g. "Place Order", "Pay Now")
- Primary:   bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white
- Secondary: bg-neutral-100 hover:bg-neutral-200 text-neutral-700
- Danger:    bg-danger-500 hover:bg-danger-600 text-white (void/delete/cancel actions)
- Ghost:     text-neutral-500 hover:bg-neutral-100 (icon-only / row actions)
- All buttons: font-medium px-4 py-2 rounded-lg transition-colors
- Always add disabled:opacity-50 disabled:cursor-not-allowed on disabled state

## STATUS BADGES
- Pill shape: inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
- Pair color with a small dot + text label — never rely on color alone
  (e.g. paid = success, pending = warning, cancelled = danger)

## TABLE RULES
- Sticky header: sticky top-0 bg-white border-b border-neutral-200
- Row height: h-12 (48px) for normal views, h-9/h-10 for dense views
- Text columns: left-align | Number/price columns: right-align + font-mono
- Status columns: center-align, use badges
- Row hover: hover:bg-neutral-50
- Row divider: divide-y divide-neutral-100

## TYPOGRAPHY
- Font: Inter (or system sans-serif fallback)
- Page titles: text-2xl font-semibold
- Section headers: text-lg font-medium
- Body / table text: text-sm (14px)
- Labels / timestamps / helper text: text-xs text-neutral-500

## CARDS / CONTAINERS
- bg-white rounded-xl border border-neutral-200 p-4 shadow-sm
- Use for: KPI cards, order cards, product cards, modal containers

## SPACING
- Only use Tailwind's default scale: p-2, p-4, p-6, gap-2, gap-4, etc.
- NEVER use arbitrary values like p-[13px] or w-[247px] unless truly unavoidable

## LAYOUT
- Sidebar width: 240–280px (w-60 to w-72)
- Dashboard KPI strip: 4–6 cards max, grid-cols-2 md:grid-cols-4 gap-4
- Main content: CSS Grid or flex, generous whitespace, no cramped sections

## BEFORE FINISHING ANY COMPONENT
- Confirm no arbitrary colors/spacing were used outside this system
- Confirm status colors match semantic meaning (success/warning/danger/info)
- Confirm only one primary button exists per view
- If you had to make a judgment call not covered here, tell me what you chose
  and why, so we can add it to this file for next time

  ## FORM INPUTS (Text, Select, Number)
- Standard Height: Use h-12 to ensure large, comfortable tap targets.
- Base: bg-white border border-neutral-200 rounded-lg px-4 text-sm text-neutral-900.
- Focus State: focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500.
- Labels: Always place labels ABOVE the input (block text-xs font-medium text-neutral-700 mb-1).
- Error State: border-danger-500 focus:ring-2 focus:ring-danger-500 text-danger-600.

## POS SELECTIONS (Radios & Checkboxes)
- NEVER use default small HTML radio/checkbox inputs on the POS screen. 
- Modifier Buttons (e.g., Ice Level, Milk Type): Style as selectable blocks.
  - Unselected: bg-neutral-50 border border-neutral-200 text-neutral-700.
  - Selected: bg-primary-50 border-primary-500 text-primary-700 ring-1 ring-primary-500.
- Sizing: Must use min-h-12 w-full flex-1 for grid alignments.
- Checkboxes (Add-ons): Use a standard check icon on the left, but make the ENTIRE container row clickable.

## INTERACTIVE PRODUCT CARDS
- Base: Inherits your exact card rule (bg-white rounded-xl border border-neutral-200 p-4 shadow-sm) PLUS cursor-pointer select-none.
- Active/Tap State: active:scale-95 active:bg-neutral-50 transition-transform.
- Layout: Use aspect-square or h-32 (128px) for consistent grid alignment.
- Typography: text-sm font-medium for the title, text-xs text-neutral-500 for the price.

## TABS & NAVIGATION
- Use horizontal scrollable pills for categories.
- Active Tab: bg-neutral-900 text-white rounded-full px-4 py-2 text-sm font-medium (using your darkest neutral token).
- Inactive Tab: text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 px-4 py-2 text-sm font-medium transition-colors.

## MODALS & OVERLAYS
- Backdrop: bg-neutral-900/50 backdrop-blur-sm fixed inset-0 z-50 (uses your exact neutral-900 token).
- Container: Inherits your card rule but larger (bg-white rounded-xl border border-neutral-200 p-6 shadow-xl w-full max-w-lg mx-auto mt-20).
- Header: Must include a clear title (text-lg font-medium) and an "X" close button (Ghost Button style) in the top right.
- Footer: Actions pinned to the bottom. Cancel on the left (Secondary Button), Confirm on the right (Primary or Danger Button).