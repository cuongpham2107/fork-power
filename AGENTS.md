# Agent Guidelines for ForkPower Laravel Application

## Commands

### Development Server
- **Start dev server**: `php artisan serve`
- **Start with all services**: `npm run dev` (runs artisan serve, queue:listen, pail, vite concurrently via concurrently)
- **Start specific service**: `php artisan queue:listen` or `php artisan pail` or `npm run dev`

### Testing (Pest/PHPUnit)
- **Run all tests**: `php artisan test` or `vendor/bin/pest`
- **Run single test by name**: `php artisan test --filter=test_method_name` or `vendor/bin/pest --filter=test_method_name`
- **Run specific test file**: `php artisan test tests/Feature/ExampleTest.php`
- **Run with coverage**: `php artisan test --coverage`
- **Run only feature tests**: `php artisan test --testsuite=Feature`
- **Run only unit tests**: `php artisan test --testsuite=Unit`
- **Run tests in watch mode**: `vendor/bin/pest --watch`
- **Clear config before test**: `php artisan config:clear` (run automatically via `composer test`)

### Linting and Formatting
- **Format PHP (Pint)**: `php artisan pint` or `composer pint` (if alias)
- **Check PHP format**: `php artisan pint --test`
- **Build frontend**: `npm run build`
- **Dev frontend**: `npm run dev`
- **No ESLint/Prettier configured** - follow Pint for PHP, Tailwind for CSS.

### Database
- **Run migrations**: `php artisan migrate`
- **Fresh migrate**: `php artisan migrate:fresh`
- **Fresh migrate with seed**: `php artisan migrate:fresh --seed`
- **Rollback**: `php artisan migrate:rollback`
- **Create migration**: `php artisan make:migration create_table`
- **Create model**: `php artisan make:model ModelName`
- **Create factory**: `php artisan make:factory ModelNameFactory`
- **Create seeder**: `php artisan make:seeder ModelNameSeeder`

### Package Management
- **Install deps**: `composer install && npm install`
- **Update deps**: `composer update && npm update`
- **Add PHP package**: `composer require vendor/package`
- **Add NPM package**: `npm install package`

### Setup New Environment
- **Setup script**: `composer setup` (installs deps, copies .env, generates key, migrates, builds assets)

## Code Style Guidelines

### PHP (Laravel 12 + Filament 5)
- **PHP version**: 8.2+
- **Formatting**: Laravel Pint (PSR-12 based)
- **Strict typing**: Always `declare(strict_types=1);`
- **Imports**: PSR-4 autoload, alphabetical, grouped: built-in → third-party → App\ → same namespace
- **Arrays**: Short syntax `[]`, trailing comma in multiline
- **Strings**: Single quotes, double for interpolation
- **Naming**:
  - Classes: `StudlyCase`
  - Methods/variables: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`
  - Files: `PascalCase` for classes, `snake_case` for config/views
- **Type hints**: Required for parameters and returns, union types when appropriate, nullable `?Type`
- **Error handling**: Use Laravel validation, throw specific exceptions, catch specific, log with `Log::`
- **PHPDoc**: For public/protected methods, include `@param`, `@return`, `@throws`
- **Blade**: Use `{{ }}` for output, `{!! !!}` for trusted HTML, alternative syntax for control structures

### JavaScript/TypeScript
- **Format**: No ESLint configured, follow common conventions
- **Naming**: `camelCase` variables/functions, `PascalCase` components
- **Imports**: Named imports, alphabetical
- **Equality**: `===`/`!==`
- **Arrow functions**: Prefer for callbacks
- **Modules**: ES6 import/export

### CSS/Tailwind v4
- **Utility-first**: Follow Tailwind v4 conventions
- **Responsive**: Mobile-first with `sm:`, `md:`, `lg:`, etc.
- **Dark mode**: Use `dark:` prefix
- **Custom styles**: Use `@apply` for component classes
- **Performance**: Extract repeated utility groups to components

### Filament 5 Specific
- **Resources**: Extend `Filament\Resources\Resource`
- **Forms**: Use `Filament\Forms\Form` with components
- **Tables**: Use `Filament\Tables\Table` with columns/actions
- **Pages**: Extend `Filament\Pages\Page`
- **Widgets**: Extend `Filament\Widgets\Widget`
- **Actions**: Use `Filament\Actions\Action` for button/modal actions
- **Authorization**: Integrate with Laravel gates/policies
- **Validation**: In form schemas, use Laravel validation rules

### General Laravel Conventions
- **Routing**: Resource routes where appropriate, API routes in `routes/api.php`
- **Controllers**: Thin, delegate to services/actions
- **Models**: Use Eloquent relationships, scopes, mutators/accessors
- **Validation**: Form Requests for complex rules
- **Jobs/Events**: Use for decoupling, queued for long tasks
- **Testing**: Pest preferred, feature tests for HTTP, unit for logic, `RefreshDatabase` trait
- **Security**: Validate inputs, use CSRF protection, environment variables for secrets
- **Performance**: Eager load relationships, use indexes, cache expensive operations

## Best Practices

### Testing Conventions
- **Pest syntax**: Prefer Pest over PHPUnit for new tests
- **Test structure**: Feature tests in `tests/Feature`, unit tests in `tests/Unit`
- **RefreshDatabase**: Use `RefreshDatabase` trait for tests that modify database
- **Factories**: Use model factories for test data
- **Mocking**: Mock external services and APIs
- **Test naming**: Use descriptive names, `it_` prefix for Pest
- **Run single test**: Use `--filter=test_name` or `pest --filter=test_name`
- **Browser tests**: Use Laravel Dusk for browser automation if needed

### Error Handling & Logging
- **Exceptions**: Throw specific exceptions (`ValidationException`, `NotFoundException`)
- **Validation**: Use Form Request classes for complex validation rules
- **Logging**: Use `Log::info()`, `Log::error()` with context arrays
- **Global handler**: Customize `app/Exceptions/Handler.php` for error rendering
- **Debugbar**: Enable in development for debugging (if installed)

### Security Guidelines
- **Inputs**: Validate and sanitize all user inputs
- **SQL injection**: Use Eloquent or query builder with bindings
- **XSS**: Escape output in Blade templates with `{{ }}`
- **CSRF**: Include `@csrf` in forms, verify in API routes
- **Authentication**: Use Laravel's built-in auth (Sanctum for API tokens)
- **Authorization**: Implement policies for resource access control
- **Environment**: Never commit `.env` file, use environment variables

### Performance Optimization
- **Eager loading**: Use `with()` to prevent N+1 queries
- **Database indexes**: Add indexes for foreign keys and frequently queried columns
- **Caching**: Cache expensive queries with `Cache::remember()`
- **Queues**: Offload long tasks to queues (`ShouldQueue`)
- **Asset bundling**: Use Vite for efficient frontend asset compilation
- **Image optimization**: Use Intervention Image or similar for image processing

### Common Patterns
- **Service classes**: Business logic in `app/Services/` directory
- **Actions**: Single-purpose action classes for complex operations
- **DTOs**: Data Transfer Objects for structured data passing
- **Repositories**: Optional data abstraction layer (if needed)
- **Middleware**: Cross-cutting concerns in `app/Http/Middleware/`
- **Events/Listeners**: Decouple side effects from main logic

## Dashboard Features

### Overview
The admin dashboard includes comprehensive statistics and charts for battery and forklift management:

1. **Battery Usage Statistics** - Monthly, quarterly, and yearly usage counts
2. **Battery Status** - Doughnut chart showing active, standby, maintenance batteries
3. **Forklift Status** - Doughnut chart showing active, inactive, maintenance forklifts
4. **Usage Trend Chart** - Line chart showing battery usage over 12 months

### Dashboard Widgets
Located in `app/Filament/Widgets/`:
- `BatteryStatsOverview.php` - Stats overview widget
- `BatteryUsageChart.php` - Usage trend line chart
- `BatteryStatusChart.php` - Battery status doughnut chart
- `ForkLiftStatusChart.php` - Forklift status doughnut chart

### Custom Dashboard
- Custom dashboard page at `app/Filament/Pages/Dashboard.php`
- Registers all widgets with specific ordering
- Access via `/admin/dashboard` after login

## Notes
- No `.cursor/` or `.cursorrules` found.
- No `.github/copilot-instructions.md` found.
- Vite used for frontend asset bundling (Tailwind CSS v4).
- Pail for real-time log viewing, queue:listen for job processing.
- Boost and Sail are dev dependencies (Sail for Docker, Boost for Laravel Boost).
- Laravel Boost installed for AI coding assistance and helper functions.
- Default login: `test@example.com` / `password`