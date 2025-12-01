# Clean Architecture - Laravel Project

## Struktur Folder

```
app/
├── Actions/              # Single-purpose action classes
│   ├── User/            # User-related actions
│   └── Auth/            # Authentication actions
│
├── Services/            # Business logic & orchestration
│   └── UserService.php  # Example service
│
├── DTOs/                # Data Transfer Objects
│   └── UserData.php     # Example DTO
│
├── Repositories/        # Data access layer implementations
│   └── UserRepository.php
│
├── Contracts/           # Interfaces for dependency injection
│   └── UserRepositoryInterface.php
│
├── Enums/              # Enumerations (PHP 8.1+)
│   └── UserStatus.php  # Example enum
│
├── Models/             # Eloquent models
├── Http/               # HTTP layer
│   ├── Controllers/    # Thin controllers
│   ├── Requests/       # Form request validation
│   ├── Resources/      # API resources
│   └── Middleware/     # HTTP middleware
│
├── Console/            # Artisan commands
├── Exceptions/         # Custom exceptions
└── Providers/          # Service providers
```

## Penjelasan Layer

### 1. **Actions** (Single Responsibility)
- Satu class untuk satu action/operasi
- Reusable di berbagai tempat (Controller, Job, Command)
- Contoh: `CreateUserAction`, `SendEmailAction`, `ProcessPaymentAction`

**Kapan pakai:**
- Operasi yang dapat digunakan berulang kali
- Logic yang simpel dan fokus pada satu tugas

**Example:**
```php
// app/Actions/User/CreateUserAction.php
class CreateUserAction
{
    public function execute(UserData $data): User
    {
        return User::create([...]);
    }
}
```

### 2. **Services** (Orchestration)
- Mengkoordinasi beberapa Actions
- Business logic yang kompleks
- Contoh: `UserService`, `OrderService`, `PaymentService`

**Kapan pakai:**
- Ketika butuh beberapa actions dalam satu workflow
- Business logic yang kompleks

**Example:**
```php
// app/Services/UserService.php
class UserService
{
    public function registerUser(UserData $data): User
    {
        $user = $this->createUserAction->execute($data);
        $this->sendWelcomeEmailAction->execute($user);
        return $user;
    }
}
```

### 3. **DTOs** (Data Transfer Objects)
- Immutable data objects
- Type-safe data passing
- Validation di satu tempat

**Kapan pakai:**
- Transfer data antar layer
- Menghindari passing Request object ke Service/Action

**Example:**
```php
// app/DTOs/UserData.php
class UserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
```

### 4. **Repositories** (Data Access)
- Abstraksi untuk data access
- Query kompleks
- Caching layer

**Kapan pakai:**
- Query yang kompleks dan sering dipakai
- Butuh abstraksi dari Eloquent (untuk testing)

**Example:**
```php
// app/Repositories/UserRepository.php
class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User {...}
    public function findByEmail(string $email): ?User {...}
}
```

### 5. **Contracts** (Interfaces)
- Interface untuk dependency injection
- Loose coupling
- Mudah untuk testing (mocking)

**Example:**
```php
// app/Contracts/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
}
```

### 6. **Enums** (PHP 8.1+)
- Type-safe constants
- Status, types, roles

**Example:**
```php
// app/Enums/UserStatus.php
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
```

## Flow Request

```
Request → Controller → Service → Action → Repository → Model
          ↓
       Validation (FormRequest)
          ↓
       DTO Creation
          ↓
       Business Logic (Service/Action)
          ↓
       Data Access (Repository/Model)
          ↓
       Response (Resource)
```

## Best Practices

### ✅ DO:
1. Keep controllers thin (only routing & validation)
2. Use DTOs for data transfer between layers
3. One Action = One Responsibility
4. Use dependency injection
5. Type hint everything (PHP 8.1+)

### ❌ DON'T:
1. Put business logic in controllers
2. Pass Request objects to Services/Actions
3. Use Eloquent directly in Controllers (for complex queries)
4. Make fat models
5. Skip validation

## Contoh Lengkap

### Controller
```php
class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function store(CreateUserRequest $request)
    {
        $user = $this->userService->registerUser(
            UserData::fromRequest($request)
        );

        return new UserResource($user);
    }
}
```

### Service
```php
class UserService
{
    public function __construct(
        private CreateUserAction $createUser,
        private SendWelcomeEmailAction $sendEmail
    ) {}

    public function registerUser(UserData $data): User
    {
        $user = $this->createUser->execute($data);
        $this->sendEmail->execute($user);
        return $user;
    }
}
```

### Action
```php
class CreateUserAction
{
    public function execute(UserData $data): User
    {
        return User::create($data->toArray());
    }
}
```

## Binding di Service Provider

Jangan lupa register interface binding:

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->bind(
        UserRepositoryInterface::class,
        UserRepository::class
    );
}
```

## Testing

```php
// Unit Test - Action
test('can create user', function () {
    $data = new UserData('John', 'john@example.com', 'password');
    $action = new CreateUserAction();

    $user = $action->execute($data);

    expect($user)->toBeInstanceOf(User::class);
});

// Integration Test - Service
test('can register user', function () {
    $service = app(UserService::class);
    $data = new UserData('John', 'john@example.com', 'password');

    $user = $service->registerUser($data);

    expect($user)->toBeInstanceOf(User::class);
    // Assert email sent...
});
```

## Resources

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Clean Architecture by Uncle Bob](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
