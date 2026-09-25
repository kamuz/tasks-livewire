# Плануй

«Плануй» — персональний список справ із реєстрацією користувачів.

## Можливості

- Реєстрація та вхід користувача;
- Захист сторінки задач через `auth` middleware;
- Створення задач із назвою та описом;
- Позначення задач виконаними або активними;
- Фільтрація: усі / активні / виконані;
- Видалення задач;
- Статистика активних і виконаних задач;
- Вихід із системи;
- Обмеження кількості невдалих спроб входу через rate limiter.

Головна сторінка перенаправляє на `/tasks`. Є також Livewire-компонент для контактів, модель `Contact` і таблиця `contacts`, але маршрут для контактів наразі не підключений до `routes/web.php`.

## Технології

| Частина             | Технологія                                      |
|---------------------|-------------------------------------------------|
| Backend             | PHP 8.3+                                        |
| Framework           | Laravel 13.17                                   |
| Інтерактивність     | Livewire 4.4                                    |
| UI-компоненти       | Livewire Single-File Components                 |
| Оптимізація Blade   | Livewire Blaze 1.x                              |
| Шаблони             | Blade                                           |
| CSS                 | Tailwind CSS 4                                  |
| Frontend-збірка     | Vite 8 через Vite Plus                          |
| Шрифт               | Instrument Sans                                 |
| База даних          | SQLite                                          |
| ORM                 | Eloquent                                        |
| Аутентифікація      | Laravel session authentication                  |
| Тестування          | Pest 5 + PHPUnit                                |
| Статичний аналіз    | Larastan 3                                      |
| Форматування PHP    | Laravel Pint                                    |
| AI-інтеграція       | Laravel Boost + MCP для GitHub Copilot          |
| Локальне оточення   | Laravel Sail присутній серед dev-залежностей    |

## Архітектура

Це Laravel-застосунок із маршрутизацією через `routes/web.php`, моделями `User`, `Task` і `Contact`, міграціями SQLite та Livewire-компонентами в `resources/views/components/`.

Інтерфейс повністю стилізований Tailwind-класами, а основна логіка задач виконується серверною частиною Livewire без окремого frontend-фреймворку на кшталт React чи Vue.

## Покрокове створення проєкту

Нижче описано, як створити подібний застосунок із нуля. Приклади команд призначені для Windows PowerShell, macOS і Linux. Команди потрібно виконувати з папки, у якій ви хочете створити проєкт.

### 1. Підготуйте середовище

Встановіть:

- PHP 8.3 або новішої версії;
- Composer — менеджер PHP-залежностей;
- Node.js та npm — для збирання CSS і JavaScript;
- SQLite — проста файлова база даних, зручна для навчальних проєктів;
- Laravel Installer — необов’язково, але спрощує створення застосунків.

Перевірте встановлені інструменти:

```bash
php --version
composer --version
node --version
npm --version
```

### 2. Створіть Laravel-проєкт

Встановіть Laravel Installer, якщо його ще немає:

```bash
composer global require laravel/installer
```

Створіть новий проєкт:

```bash
laravel new contacts-livewire
cd contacts-livewire
```

Під час створення оберіть Livewire як frontend-стек і SQLite як базу даних, якщо інсталятор пропонує такі запитання.

Якщо команда `laravel` недоступна, можна використати Composer:

```bash
composer create-project laravel/laravel contacts-livewire
cd contacts-livewire
```

#### Що створив Laravel?

- `app/` — PHP-код застосунку: моделі, контролери та сервіси;
- `routes/` — маршрути, тобто адреси сторінок і дії, які вони викликають;
- `resources/views/` — Blade-шаблони інтерфейсу;
- `database/migrations/` — опис структури таблиць бази даних;
- `database/factories/` і `database/seeders/` — тестові дані;
- `public/` — публічні файли, доступні браузеру;
- `.env` — локальні налаштування, наприклад підключення до бази;
- `composer.json` і `package.json` — списки PHP та JavaScript-залежностей.

### 3. Встановіть Livewire і Tailwind CSS

Для цього проєкту використовуються Livewire 4 і Tailwind CSS 4:

```bash
composer require livewire/livewire
npm install
```

Livewire дозволяє створювати інтерактивні компоненти переважно на PHP. Наприклад, натискання кнопки `wire:click="toggleTask"` викликає PHP-метод компонента, а Livewire оновлює потрібну частину HTML без повного перезавантаження сторінки.

Tailwind CSS — це набір CSS-класів для стилізації HTML. Наприклад, `rounded-xl`, `bg-indigo-600` і `px-4` задають відповідно заокруглення, колір фону та горизонтальні відступи.

### 4. Налаштуйте базу даних

У файлі `.env` вкажіть SQLite:

```dotenv
DB_CONNECTION=sqlite
```

Створіть файл бази даних, якщо його немає:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

Запустіть стандартні міграції:

```bash
php artisan migrate
```

#### Що таке міграція?

Міграція — це версійний опис структури бази даних. Замість ручного створення таблиць ви описуєте їх у PHP-файлі, а Laravel створює або змінює таблиці командою `php artisan migrate`.

### 5. Створіть модель задачі та міграцію

Модель — це PHP-клас, який представляє запис у таблиці бази даних. Створіть модель `Task` разом із міграцією та фабрикою:

```bash
php artisan make:model Task -mf
```

У міграції опишіть поля задачі:

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->boolean('is_completed')->default(false);
    $table->timestamps();

    $table->index(['user_id', 'is_completed']);
});
```

Тут:

- `id()` створює унікальний ідентифікатор;
- `foreignId('user_id')` пов’язує задачу з користувачем;
- `nullable()` дозволяє залишити опис порожнім;
- `boolean()` зберігає значення `true` або `false`;
- індекс прискорює пошук задач конкретного користувача за статусом.

Застосуйте міграцію:

```bash
php artisan migrate
```

### 6. Опишіть зв’язок між користувачем і задачами

Один користувач може мати багато задач, а кожна задача належить одному користувачу. У моделі `User` використовуйте зв’язок `hasMany`:

```php
public function tasks(): HasMany
{
    return $this->hasMany(Task::class);
}
```

У моделі `Task` додайте зворотний зв’язок `belongsTo`:

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

Так Laravel дозволяє писати зрозумілий код:

```php
$user->tasks()->create($data);
$task->user;
```

### 7. Додайте автентифікацію

Автентифікація визначає, хто саме працює із застосунком. Для кожного користувача Laravel зберігає запис у таблиці `users`, а браузер підтримує вхід через сесію.

Реєстрація зазвичай виконує такі дії:

1. перевіряє ім’я, email і пароль;
2. створює користувача;
3. автоматично входить у створений акаунт;
4. перенаправляє на сторінку задач.

Вхід перевіряє email і пароль:

```php
if (Auth::attempt($credentials)) {
    request()->session()->regenerate();

    return $this->redirectRoute('tasks.index');
}
```

`session()->regenerate()` захищає сесію від session fixation — атаки, під час якої зловмисник намагається використати заздалегідь відомий ідентифікатор сесії.

Для захисту маршруту використовуйте middleware:

```php
Route::livewire('/tasks', 'tasks.index')
    ->middleware('auth')
    ->name('tasks.index');
```

Користувач без входу не зможе відкрити сторінку задач.

### 8. Створіть Livewire-компонент задач

У Livewire 4 можна створювати Single-File Component — компонент, у якому PHP-клас і Blade-розмітка знаходяться в одному файлі:

```bash
php artisan make:livewire tasks.index
```

Компонент містить стан і дії. Стан — це властивості, наприклад назва нової задачі та поточний фільтр:

```php
public string $title = '';

public string $description = '';

public string $filter = 'all';
```

Дія додавання задачі перевіряє дані та створює запис для поточного користувача:

```php
public function addTask(): void
{
    $validated = $this->validate([
        'title' => ['required', 'string', 'max:160'],
        'description' => ['nullable', 'string', 'max:1000'],
    ]);

    auth()->user()->tasks()->create($validated);
    $this->reset('title', 'description');
}
```

`$this->validate()` не дозволяє зберегти некоректні дані. Важливо створювати задачу через `auth()->user()->tasks()`, а не через загальну модель `Task`: так користувач не зможе випадково працювати з чужими задачами.

### 9. Зв’яжіть форму з PHP-властивостями

У Blade-розмітці Livewire використовуйте `wire:model`:

```blade
<input type="text" wire:model="title">
<textarea wire:model="description"></textarea>
<form wire:submit="addTask">
    ...
</form>
```

Коли користувач заповнює поле, значення потрапляє у властивість компонента. Після відправлення форми Livewire викликає `addTask()` і оновлює сторінку без повного перезавантаження.

Для списку задач використовуйте цикл Blade:

```blade
@foreach ($tasks as $task)
    <article wire:key="task-{{ $task->id }}">
        {{ $task->title }}
    </article>
@endforeach
```

`wire:key` допомагає Livewire правильно розрізняти елементи списку під час оновлення DOM.

### 10. Додайте фільтрацію, завершення та видалення

Фільтрація будується умовами запиту:

```php
$tasks = auth()->user()->tasks()
    ->when($filter === 'active', fn ($query) => $query->where('is_completed', false))
    ->when($filter === 'completed', fn ($query) => $query->where('is_completed', true))
    ->latest()
    ->get();
```

Перемикання статусу спочатку знаходить задачу серед задач поточного користувача:

```php
public function toggleTask(int $taskId): void
{
    $task = auth()->user()->tasks()->findOrFail($taskId);
    $task->update(['is_completed' => ! $task->is_completed]);
}
```

Цей підхід одночасно виконує перевірку належності задачі користувачу. Якщо задача чужа або не існує, Laravel повертає помилку `404`.

### 11. Додайте маршрути

У `routes/web.php` маршрути зв’язують URL із Livewire-компонентами:

```php
Route::livewire('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');

Route::livewire('/register', 'auth.register')
    ->middleware('guest')
    ->name('register');

Route::livewire('/tasks', 'tasks.index')
    ->middleware('auth')
    ->name('tasks.index');
```

Іменовані маршрути дозволяють створювати посилання через `route('tasks.index')`, не дублюючи URL у різних файлах.

### 12. Додайте оформлення

Підключіть Tailwind у `resources/css/app.css`:

```css
@import 'tailwindcss';

@source '../views';
```

`@source` повідомляє Tailwind, де шукати використані класи. Під час збірки Tailwind залишає лише потрібні стилі.

У layout-файлі підключіть CSS і JavaScript:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
...
@livewireScripts
```

Layout — це спільна оболонка сторінок: HTML-документ, заголовок, стилі та скрипти. Компоненти підставляють свій вміст у `{{ $slot }}`.

### 13. Запустіть застосунок

В одному терміналі запустіть Laravel:

```bash
php artisan serve
```

В іншому терміналі запустіть Vite у режимі розробки:

```bash
npm run dev
```

Після цього відкрийте адресу, яку покаже `php artisan serve`, зазвичай `http://127.0.0.1:8000`.

Для production-збірки використовуйте:

```bash
npm run build
```

### 14. Додайте тестування

Створіть feature-тест:

```bash
php artisan make:test --pest TaskTest
```

Feature-тести перевіряють поведінку застосунку з точки зору користувача: маршрути, автентифікацію, створення задач і відповіді сервера.

Запустіть тести:

```bash
php artisan test
```

Для Livewire-компонентів можна перевіряти стан і виклики методів:

```php
Livewire::test('tasks.index')
    ->set('title', 'Вивчити Laravel')
    ->call('addTask')
    ->assertSet('title', '');
```

### 15. Підключіть Laravel Boost для GitHub Copilot

Laravel Boost надає AI-агентам контекст про Laravel, структуру застосунку, документацію та MCP-інструменти.

Встановіть Boost як dev-залежність:

```bash
composer require laravel/boost --dev
```

Згенеруйте конфігурацію:

```bash
php artisan boost:install
```

У цьому репозиторії вже налаштовані:

- `boost.json` — вибрані агенти, guidelines і skills;
- `AGENTS.md` — інструкції для AI-агентів;
- `.mcp.json` — локальний MCP-сервер `laravel-boost`.

MCP (Model Context Protocol) — це стандарт, через який AI-агент може безпечним способом отримувати структурований контекст про проєкт, документацію та середовище Laravel.

Оновити ресурси Boost можна командою:

```bash
php artisan boost:update
```

## Корисні команди Artisan

| Команда                                  | Призначення                              |
|------------------------------------------|------------------------------------------|
| `php artisan serve`                       | Запустити локальний веб-сервер            |
| `php artisan migrate`                     | Застосувати міграції                      |
| `php artisan migrate:fresh`               | Пересоздати всі таблиці                   |
| `php artisan make:model Task -mf`         | Створити модель, міграцію та фабрику      |
| `php artisan make:livewire tasks.index`   | Створити Livewire-компонент               |
| `php artisan route:list`                  | Переглянути всі маршрути                  |
| `php artisan test`                        | Запустити тести                            |
| `php artisan boost:update`                | Оновити ресурси Laravel Boost              |

> `migrate:fresh` видаляє всі таблиці та створює їх заново. Не використовуйте цю команду в production без повного розуміння наслідків.
