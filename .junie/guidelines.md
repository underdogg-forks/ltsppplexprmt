# LetsPeppol API Client Architecture Guidelines

## Overview

The LetsPeppol API client follows a modern, enterprise-grade architecture using design patterns that promote maintainability, testability, and extensibility.

## Architecture Patterns

### 1. Decorator Pattern

The API client uses a decorator chain for cross-cutting concerns:

```
RequestLogger → HttpExceptionHandler → HttpClient
```

- **RequestLogger**: Logs all API requests and responses with timing information using the `LogsActivity` trait
- **HttpExceptionHandler**: Converts HTTP errors into domain-specific exceptions
- **HttpClient**: Base HTTP client that makes actual API calls with `->throw()` for automatic exception handling

### 2. Service-Oriented Architecture

The client is organized into three main services:

- **KycService**: Authentication and registration endpoints
- **ProxyService**: Document transmission and registry endpoints
- **AppService**: Application management endpoints

Each service uses the decorator chain and provides access to endpoint-specific clients.

### 3. Endpoint-Specific Clients

Instead of monolithic client classes, each API endpoint has its own dedicated client:

```
app/Services/LetsPeppol/Endpoints/
├── AuthenticationEndpoint.php (KycService)
├── CompanyEndpoint.php (AppService)
├── DocumentsEndpoint.php (AppService)
├── PartnersEndpoint.php (AppService)
├── PasswordEndpoint.php (KycService)
├── PeppolDirectoryEndpoint.php (AppService)
├── ProductCategoriesEndpoint.php (AppService)
├── ProductsEndpoint.php (AppService)
├── ProxyDocumentsEndpoint.php (ProxyService)
├── RegistrationEndpoint.php (KycService)
├── RegistryEndpoint.php (ProxyService)
├── StatisticsEndpoint.php (AppService)
└── MonitorEndpoint.php (ProxyService)
```

Each endpoint client includes:
- Namespace documentation (KYC/App/Proxy) in the class docblock
- JSON request/response structure documentation for each method

### 4. Interface Segregation

The `ClientInterface` defines the contract that all HTTP clients must implement:

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

### 5. LogsActivity Trait

All classes that need logging should use the `LogsActivity` trait:

```php
use App\Services\LetsPeppol\Traits\LogsActivity;

class RequestLogger extends ClientDecorator
{
    use LogsActivity;
    
    public function someMethod(): void
    {
        $this->logInfo('Processing request', ['endpoint' => '/api/test']);
        $this->logError('Request failed', ['error' => $e->getMessage()]);
        $this->logWarning('Slow response', ['duration' => 5000]);
    }
}
```

Benefits:
- Automatic class context in log messages
- Consistent logging format across all components
- No direct `Log` facade calls

## Usage Examples

### Basic Authentication

```php
$client = new LetsPeppolClient();
$token = $client->authenticate('user@example.com', 'password');
// Token is automatically set for all services
```

### Using Endpoints

```php
// Documents
$documents = $client->app()->documents()->list();
$document = $client->app()->documents()->get($id);
$client->app()->documents()->create($ublXml);

// Paginate through all documents
$client->app()->documents()->listAll(function($documents) {
    foreach ($documents as $doc) {
        // Process each document
    }
}, ['type' => 'INVOICE'], 50);

// Partners
$partners = $client->app()->partners()->list();
$partner = $client->app()->partners()->create($data);

// Company
$company = $client->app()->company()->get();
$client->app()->company()->update($data);
```

### Error Handling

The client throws domain-specific exceptions:

```php
use App\Services\LetsPeppol\Exceptions\AuthenticationException;
use App\Services\LetsPeppol\Exceptions\NotFoundException;
use App\Services\LetsPeppol\Exceptions\ValidationException;

try {
    $document = $client->app()->documents()->get($id);
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (NotFoundException $e) {
    // Handle not found errors
} catch (ValidationException $e) {
    // Handle validation errors
}
```

## Request Method Enum

All HTTP methods are represented by the `RequestMethod` enum:

```php
enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
}
```

## Logging

The `RequestLogger` decorator automatically logs using the `LogsActivity` trait:
- Request method, endpoint, and query parameters
- Response success/failure status
- Request duration in milliseconds
- Error messages for failed requests
- Automatic class context (e.g., "[RequestLogger] API Request")

All logs use Laravel's standard logging facade through the trait.

## Testing

The architecture is designed for testability following these conventions:

### Test Naming Convention

- All test methods start with `it_` and make grammatical sense
- Use `#[Test]` PHP 8 attribute instead of `test` prefix
- Follow "Arrange, Act, Assert" pattern

Example:

```php
use PHPUnit\Framework\Attributes\Test;

class DocumentsEndpointTest extends TestCase
{
    #[Test]
    public function it_retrieves_document_by_id(): void
    {
        // Arrange
        $documentId = 'test-doc-123';
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('request')
            ->willReturn(['id' => $documentId]);

        // Act
        $endpoint = new DocumentsEndpoint($mockClient);
        $result = $endpoint->get($documentId);

        // Assert
        $this->assertEquals($documentId, $result['id']);
    }
}
```

### Testing Approaches

1. **Mock the ClientInterface**: Replace the HTTP client with a test double
2. **Test Endpoints in Isolation**: Each endpoint can be tested independently
3. **Test Decorators**: Decorators can be tested with mock clients

## Pagination Helper

For endpoints with pagination, use the `listAll()` helper with a do...while loop:

```php
// In endpoint class
public function listAll(callable $callback, array $filters = [], int $size = 20, ?string $sort = null): void
{
    $page = 0;
    
    do {
        $response = $this->list($filters, $page, $size, $sort);
        $callback($response['content'] ?? []);
        $page++;
        $hasMore = !empty($response['content']) && count($response['content']) === $size;
    } while ($hasMore);
}
```

## Adding New Endpoints

To add a new endpoint:

1. Create a new class extending `BaseEndpoint`
2. Add namespace documentation in class docblock (KYC/App/Proxy)
3. Document JSON structures for each method
4. Add methods using `$this->request()` with `RequestMethod` enum
5. Register the endpoint in the appropriate service

Example:

```php
/**
 * New endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/new-endpoint
 */
class NewEndpoint extends BaseEndpoint
{
    /**
     * List items
     * 
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "name": "Item 1"
     *   }
     * ]
     */
    public function list(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/new-endpoint'
        );
    }

    /**
     * Create item
     * 
     * Request:
     * {
     *   "name": "New Item"
     * }
     * 
     * Response:
     * {
     *   "id": 1,
     *   "name": "New Item"
     * }
     */
    public function create(array $data): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/new-endpoint',
            $data
        );
    }
}
```

## SOLID Principles Applied

- **Single Responsibility**: Each endpoint handles one resource
- **Open/Closed**: Extend via new endpoints, not modification
- **Liskov Substitution**: All clients implement ClientInterface
- **Interface Segregation**: Small, focused ClientInterface
- **Dependency Inversion**: Depend on ClientInterface, not concrete implementations

## Configuration

Services use configuration from `config/services.php`:

```php
'letspeppol' => [
    'kyc_url' => env('LETSPEPPOL_KYC_URL', 'https://kyc.letspeppol.org'),
    'proxy_url' => env('LETSPEPPOL_PROXY_URL', 'https://proxy.letspeppol.org'),
    'app_url' => env('LETSPEPPOL_APP_URL', 'https://app.letspeppol.org'),
],
```

## Service Provider

The `LetsPeppolServiceProvider` binds the `ClientInterface` with the decorator chain:

```php
$this->app->bind(ClientInterface::class, function ($app) {
    $baseUrl = config('services.letspeppol.app_url');
    
    return new RequestLogger(
        new HttpExceptionHandler(
            new HttpClient($baseUrl)
        )
    );
});
```

Register the provider in `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\LetsPeppolServiceProvider::class,
],
```
