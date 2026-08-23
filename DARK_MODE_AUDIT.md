# Dark Mode Audit Report

## Issues Found:

### 1. Pages with Hardcoded Cards (NOT using BaseCard):
- **Products/Index.vue**: 11 hardcoded cards
  - 4 stat cards (lines 136-163)
  - 1 filter card (line 175)
  - 1 table container (line 221)
  - 1 form card (line 285)
  - 1 restock alert card (line 364)
  - Multiple cards without dark: classes

- **Reports/Index.vue**: 7 hardcoded cards
  - 4 stat cards (lines 218-245)
  - 1 form card (line 256)
  - 1 table container (line 305)
  - 1 modal (line 375)

- **Orders/Index.vue**: PARTIALLY FIXED
  - Stats cards: ✅ Fixed (using BaseCard)
  - Filter section: HAS DUPLICATE CODE (lines 249-281 and 283-315)
  - Table section: ✅ Using BaseCard with flush
  - Missing dark mode classes in table header/body

- **Profile/Edit.vue**: 3 hardcoded cards
  - All form containers need BaseCard

### 2. Elements Missing Dark Mode Classes:

**Orders/Index.vue**:
- Table header: `bg-white` needs `dark:bg-neutral-800`
- Table body text: `text-neutral-700` needs `dark:text-neutral-300`
- Table hover: `hover:bg-neutral-50` needs `dark:hover:bg-neutral-800`
- Border colors: `border-neutral-200` needs `dark:border-neutral-700`
- Pagination buttons need dark mode classes

**Products/Index.vue**:
- All text elements need dark: variants
- All borders need dark: variants
- All background colors need dark: variants
- Form inputs need dark mode styling

**Reports/Index.vue**:
- Notification toast needs dark mode
- Modal needs dark mode
- Table needs dark mode classes
- Form inputs need dark mode styling

**Dashboard.vue**:
- Quick action cards already using BaseCard ✅
- Text elements mostly have dark: classes ✅
- Recent orders table needs dark mode review

### 3. Components Needing Review:
- Pos/OrderScreen.vue
- Profile/Partials/*.vue
- Auth pages

## Fix Priority:
1. Remove duplicate code in Orders/Index.vue
2. Fix Products/Index.vue (highest usage page)
3. Fix Reports/Index.vue
4. Fix Profile pages
5. Add dark mode to tables across all pages
