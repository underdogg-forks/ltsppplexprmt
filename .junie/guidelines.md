# LetsPeppol API Client Architecture Guidelines

## Overview

The LetsPeppol API client follows a modern, enterprise-grade architecture using design patterns that promote maintainability, testability, and extensibility.

## Architecture Patterns

### 1. Decorator Pattern

The API client uses a decorator chain for cross-cutting concerns:

```
RequestLogger → HttpExceptionHandler → HttpClient
```

- **RequestLogger**: Logs all API requests and responses with timing information
- **HttpExceptionHandler**: Converts HTTP errors into domain-specific exceptions
- **HttpClient**: Base HTTP client that makes actual API calls

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
├── AuthenticationEndpoint.php
├── CompanyEndpoint.php
├── DocumentsEndpoint.php
├── PartnersEndpoint.php
├── PasswordEndpoint.php
├── PeppolDirectoryEndpoint.php
├── ProductCategoriesEndpoint.php
├── ProductsEndpoint.php
├── ProxyDocumentsEndpoint.php
├── RegistrationEndpoint.php
├── RegistryEndpoint.php
├── StatisticsEndpoint.php
└── MonitorEndpoint.php
```

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

The `RequestLogger` decorator automatically logs:
- Request method, endpoint, and query parameters
- Response success/failure status
- Request duration in milliseconds
- Error messages for failed requests

All logs use Laravel's standard logging facade.

## Testing

The architecture is designed for testability:

1. **Mock the ClientInterface**: Replace the HTTP client with a test double
2. **Test Endpoints in Isolation**: Each endpoint can be tested independently
3. **Test Decorators**: Decorators can be tested with mock clients

Example:

```php
// Create a mock client
$mockClient = Mockery::mock(ClientInterface::class);
$mockClient->shouldReceive('request')
    ->andReturn(['data' => 'test']);

// Test an endpoint
$endpoint = new DocumentsEndpoint($mockClient);
$result = $endpoint->list();
```

## Adding New Endpoints

To add a new endpoint:

1. Create a new class extending `BaseEndpoint`
2. Add methods for each API operation
3. Use `$this->request()` to make HTTP calls
4. Register the endpoint in the appropriate service

Example:

```php
class NewEndpoint extends BaseEndpoint
{
    public function list(): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/new-endpoint'
        );
    }

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

## Service Provider Bindings

For better testability, use `bind()` instead of `singleton()`:

```php
$this->app->bind(ClientInterface::class, function ($app) {
    return new RequestLogger(
        new HttpExceptionHandler(
            new HttpClient(config('services.letspeppol.app_url'))
        )
    );
});
```
