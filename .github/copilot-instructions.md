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
/**
 * Documents endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/document
 */
class DocumentsEndpoint extends BaseEndpoint
{
    /**
     * List documents
     * 
     * Response:
     * {
     *   "content": [...],
     *   "totalElements": 100
     * }
     */
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

### Namespace Documentation

Always document which service (KYC/App/Proxy) an endpoint belongs to:

**DO:**
```php
/**
 * Company endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/company
 */
class CompanyEndpoint extends BaseEndpoint
```

**DON'T:**
```php
/**
 * Company endpoint client
 */
class CompanyEndpoint extends BaseEndpoint
```

### JSON Structure Documentation

Document the JSON request/response structures for all methods:

**DO:**
```php
/**
 * Get company information
 * 
 * Response:
 * {
 *   "peppolId": "0208:BE0123456789",
 *   "name": "Company Name",
 *   "vatNumber": "BE0123456789",
 *   "email": "info@company.com"
 * }
 */
public function get(): array
{
    return $this->request(RequestMethod::GET, '/sapi/company');
}

/**
 * Create partner
 * 
 * Request:
 * {
 *   "peppolId": "0208:BE0987654321",
 *   "name": "Partner Company",
 *   "vatNumber": "BE0987654321"
 * }
 * 
 * Response:
 * {
 *   "id": 1,
 *   "peppolId": "0208:BE0987654321",
 *   "name": "Partner Company"
 * }
 */
public function create(array $partnerData): array
{
    return $this->request(RequestMethod::POST, '/sapi/partner', $partnerData);
}
```

**DON'T:**
```php
/**
 * Get company information
 */
public function get(): array
{
    return $this->request(RequestMethod::GET, '/sapi/company');
}
```

### Pagination Helper

For paginated endpoints, provide a `listAll()` helper using do...while:

**DO:**
```php
/**
 * Loop through all documents with pagination
 */
public function listAll(callable $callback, array $filters = [], int $size = 20): void
{
    $page = 0;
    
    do {
        $response = $this->list($filters, $page, $size);
        $callback($response['content'] ?? []);
        $page++;
        $hasMore = !empty($response['content']) && count($response['content']) === $size;
    } while ($hasMore);
}
```

**DON'T:**
```php
// Don't force users to implement pagination themselves
```

### LogsActivity Trait

Use the `LogsActivity` trait for all logging instead of direct `Log` facade calls:

**DO:**
```php
use App\Services\LetsPeppol\Traits\LogsActivity;

class RequestLogger extends ClientDecorator
{
    use LogsActivity;
    
    public function request(...): mixed
    {
        $this->logInfo('API Request', ['endpoint' => $endpoint]);
        // ...
        $this->logError('API Request Failed', ['error' => $e->getMessage()]);
    }
}
```

**DON'T:**
```php
use Illuminate\Support\Facades\Log;

class RequestLogger extends ClientDecorator
{
    public function request(...): mixed
    {
        Log::info('API Request', ['endpoint' => $endpoint]);
        // ...
        Log::error('API Request Failed', ['error' => $e->getMessage()]);
    }
}
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

### HttpClient Simplification

The `HttpClient` should use `->throw()` for automatic exception handling:

**DO:**
```php
$response = match ($method) {
    RequestMethod::GET => $http->get($url)->throw(),
    RequestMethod::POST => $http->post($url, $data)->throw(),
    // ...
};
```

**DON'T:**
```php
$response = match ($method) {
    RequestMethod::GET => $http->get($url),
    RequestMethod::POST => $http->post($url, $data),
};

if (!$response->successful()) {
    throw new \RuntimeException(...);
}
```

### Testing Standards

All test methods must follow these conventions:

1. **Naming**: Start with `it_` and make grammatical sense
2. **Annotation**: Use `#[Test]` attribute instead of `test` prefix
3. **Structure**: Follow "Arrange, Act, Assert" pattern

**DO:**
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

**DON'T:**
```php
class DocumentsEndpointTest extends TestCase
{
    public function test_get_document(): void
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('request')
            ->willReturn(['id' => 'test-doc-123']);
        $endpoint = new DocumentsEndpoint($mockClient);
        $result = $endpoint->get('test-doc-123');
        $this->assertEquals('test-doc-123', $result['id']);
    }
}
```

### Testing Guidelines

Write tests using the project's `FakeClient` helper:

```php
use App\Services\LetsPeppol\Testing\FakeClient;
use App\Services\LetsPeppol\Enums\RequestMethod;

public function test_endpoint_list(): void
{
    // Arrange
    $fakeClient = new FakeClient();
    $fakeClient->queueResponse(['data' => 'test']);
    
    $endpoint = new EndpointClient($fakeClient);
    
    // Act
    $result = $endpoint->list();
    
    // Assert
    $this->assertEquals(['data' => 'test'], $result);
    $fakeClient->assertRequestSent('/api/endpoint', RequestMethod::GET);
}
```

### Adding New Endpoints

When adding a new endpoint:

1. Create `Endpoints/NewEndpoint.php` extending `BaseEndpoint`
2. Add namespace documentation (KYC/App/Proxy)
3. Document JSON structures for all methods
4. Add methods using `$this->request()` with `RequestMethod` enum
5. Add pagination helper if applicable
6. Register in appropriate service (KycService, ProxyService, or AppService)
7. Update the example usage file
8. Write tests using `it_` naming and `#[Test]` attribute

### Service Provider Pattern

Create a `LetsPeppolServiceProvider` that binds the interface:

**DO:**
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
- [ ] Add namespace documentation (KYC/App/Proxy)
- [ ] Document JSON structures for requests/responses
- [ ] Use `RequestMethod` enum for HTTP methods
- [ ] Use `$this->request()` for all API calls
- [ ] Add pagination helper if applicable
- [ ] Use `LogsActivity` trait if logging needed
- [ ] Add to appropriate service class
- [ ] Update example usage file
- [ ] Write unit tests with `it_` naming and `#[Test]`
- [ ] Follow "Arrange, Act, Assert" pattern in tests
- [ ] Update documentation
