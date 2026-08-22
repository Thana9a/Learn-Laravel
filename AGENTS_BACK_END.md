# BACKEND RULES FOR THIS PROJECT (Laravel 11 + PHP 8.2+ + Inertia.js)

Follow this project's conventions for ALL backend work. Study existing patterns in `app/` before creating new controllers, services, repositories, or DTOs.

---

## 1. ARCHITECTURE & LAYERING

### 1.1 Controllers (`app/Http/Controllers/`)
- Thin controllers: Only handle HTTP/Inertia request-response flow.
- Inject Repository or Service interfaces via constructor dependency injection.
- Validate inputs using custom `FormRequest` classes (never validate inline in controllers).
- Transform outgoing data through DTOs (`Spatie\LaravelData\Data`) before passing to Inertia or returning as JSON.

### 1.2 Form Requests (`app/Http/Requests/`)
- Encapsulate all validation rules and authorization logic.
- Include a `toDTO()` method on the request class to cleanly transform validated data into strongly-typed DTOs.
- Reference field names defined in `lang/{locale}/attribute.php` for localized error messages.

### 1.3 Data Transfer Objects (`app/DTOs/`)
- Use Spatie `Data` class for type safety across layers.
- Implement static `fromModel($model)` and `fromRequest($request)` helper factories.

### 1.4 Repositories & Services (`app/Repositories/`, `app/Services/`)
- Repositories handle database queries and transactions (e.g. `OrderRepositoryInterface`).
- Services handle business logic, payment integrations, report calculations, and external APIs (e.g. `ReportService`).

---

## 2. LOCALIZATION (i18n / Backend Translations)

All server-side messages, flash notifications, validation errors, and attribute labels MUST be managed in the `lang/` directory.

### Directory Structure:
```text
lang/
  en/
    common.php       ← shared: buttons, actions, statuses, messages
    attribute.php     ← field labels (used for validation attributes)
    validation.php    ← custom validation messages
    product.php       ← product-related messages
    order.php         ← order-related messages
    discount.php      ← discount & promo code messages
    payment.php       ← payment & gateway messages
    auth.php          ← authentication & session messages
  km/
    common.php
    attribute.php
    validation.php
    product.php
    order.php
    discount.php
    payment.php
    auth.php
```

### Translation Rules:
1. **Never Hardcode User-Facing Text in Backend:** Always use `__('domain.key')` or `trans('domain.key')` (e.g., `__('order.created', ['order_number' => $order->order_number])`).
2. **Attribute Mapping:** Define all model and form field names in `lang/{locale}/attribute.php` to ensure consistent labels across validation messages.
3. **Flash Notifications:** Pass localized flash messages using translation keys when redirecting:
   ```php
   return redirect()->back()->with('success', __('common.messages.saved_success'));
   ```
4. **Symmetry:** Every key added to `lang/en/*.php` MUST have an equivalent translated key in `lang/km/*.php`.

---

## 3. DATABASE & MIGRATIONS
- Always ensure migrations have matching down() methods.
- Check `ERD.mmd` before modifying schema relationships or column types.
- Never edit already-migrated migration files in production; create a new migration for schema changes.

---

## 4. CODE STYLE & CONVENTIONS
- Strict types and explicit return type declarations on all methods.
- Readonly properties for injected dependencies.
- Eloquent relationships must declare explicit return types (`BelongsTo`, `HasMany`, etc.).
