# GitHub Copilot Instructions for LetsPeppol API Client

## API Client Architecture Standards

### Decorator Pattern for HTTP Clients

Always use the decorator chain pattern for API clients:

```php
RequestLogger → HttpExceptionHandler → HttpClient
```

**DO:**
```php
$client = new RequestLogger(
    new HttpExceptionHandler(
        new HttpClient($baseUrl)
    )
);
```

**DON'T:**
```php
// Don't create standalone clients without decorators
$client = new HttpClient($baseUrl);
```

### Endpoint-Specific Clients

Each API endpoint should have its own dedicated client class extending `BaseEndpoint`.

**DO:**
```php
class DocumentsEndpoint extends BaseEndpoint
{
    public function list(array $filters = []): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/document',
            [],
            $filters
        );
    }
}
```

**DON'T:**
```php
// Don't add all endpoints to a single monolithic client
class AppClient
{
    public function listDocuments() { }
    public function listPartners() { }
    public function listProducts() { }
    // ... 50 more methods
}
```

### RequestMethod Enum

Always use the `RequestMethod` enum for HTTP methods:

**DO:**
```php
$this->request(RequestMethod::GET, '/api/endpoint');
$this->request(RequestMethod::POST, '/api/endpoint', $data);
```

**DON'T:**
```php
$this->request('GET', '/api/endpoint'); // String literals
```

### Service Organization

Organize endpoints into service classes:

- **KycService**: Authentication, registration, password management
- **ProxyService**: Document transmission, registry, monitoring
- **AppService**: Application management, documents, company, partners, products

**DO:**
```php
$client->app()->documents()->list();
$client->kyc()->authentication()->authenticate($email, $password);
$client->proxy()->registry()->get();
```

**DON'T:**
```php
$client->listDocuments(); // Flat API
```

### Exception Handling

Use domain-specific exceptions:

**DO:**
```php
use App\Services\LetsPeppol\Exceptions\AuthenticationException;
use App\Services\LetsPeppol\Exceptions\NotFoundException;

try {
    $document = $client->app()->documents()->get($id);
} catch (AuthenticationException $e) {
    // Re-authenticate
} catch (NotFoundException $e) {
    // Handle not found
}
```

**DON'T:**
```php
try {
    $document = $client->app()->documents()->get($id);
} catch (\Exception $e) {
    // Generic exception handling
}
```

### Interface Implementation

All HTTP clients must implement `ClientInterface`:

```php
interface ClientInterface
{
    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed;

    public function setToken(string $token): static;
    public function getToken(): ?string;
}
```

### Logging Standards

The `RequestLogger` decorator handles all logging automatically. Do not add manual logging in endpoint methods.

**DO:**
```php
// Let the decorator handle logging
public function list(): array
{
    return $this->request(RequestMethod::GET, '/api/endpoint');
}
```

**DON'T:**
```php
public function list(): array
{
    Log::info('Calling list endpoint');
    $result = $this->request(RequestMethod::GET, '/api/endpoint');
    Log::info('Got result', $result);
    return $result;
}
```

### Testing Guidelines

Write tests that mock the `ClientInterface`:

```php
public function test_endpoint_list(): void
{
    $mockClient = Mockery::mock(ClientInterface::class);
    $mockClient->shouldReceive('request')
        ->with(RequestMethod::GET, '/api/endpoint', [], [], [])
        ->andReturn(['data' => 'test']);

    $endpoint = new EndpointClient($mockClient);
    $result = $endpoint->list();

    $this->assertEquals(['data' => 'test'], $result);
}
```

### Adding New Endpoints

When adding a new endpoint:

1. Create `Endpoints/NewEndpoint.php` extending `BaseEndpoint`
2. Add methods using `$this->request()`
3. Register in appropriate service (KycService, ProxyService, or AppService)
4. Update the example usage file
5. Write tests

### Service Provider Pattern

Use `bind()` instead of `singleton()` for better testability:

**DO:**
```php
$this->app->bind(ClientInterface::class, function ($app) {
    return new RequestLogger(
        new HttpExceptionHandler(
            new HttpClient(config('services.letspeppol.app_url'))
        )
    );
});
```

**DON'T:**
```php
$this->app->singleton(ClientInterface::class, function ($app) {
    // Singletons make testing harder
});
```

### Code Style

- Use early returns for error conditions
- Keep methods focused on a single responsibility
- Use type hints for all parameters and return values
- Document complex logic with comments
- Follow PSR-12 coding standards

### XML Content Handling

For endpoints that accept XML:

```php
public function create(string $ublXml): array
{
    return $this->request(
        RequestMethod::POST,
        '/api/document',
        ['body' => $ublXml],
        [],
        ['Content-Type' => 'text/xml']
    );
}
```

The HttpClient will automatically use `withBody()` when Content-Type is text/xml.

## Checklist for New Endpoints

- [ ] Create endpoint class extending `BaseEndpoint`
- [ ] Use `RequestMethod` enum for HTTP methods
- [ ] Use `$this->request()` for all API calls
- [ ] Add to appropriate service class
- [ ] Update example usage file
- [ ] Write unit tests
- [ ] Update documentation
