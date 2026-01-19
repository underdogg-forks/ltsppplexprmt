# LetsPeppol API Client Architecture

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     LetsPeppolClient                        │
│                  (Main Entry Point)                         │
└──────┬───────────────────┬──────────────────┬───────────────┘
       │                   │                  │
       ▼                   ▼                  ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ KycService  │    │ProxyService │    │ AppService  │
└──────┬──────┘    └──────┬──────┘    └──────┬──────┘
       │                  │                   │
       │                  │                   │
       │         Uses Decorator Chain         │
       │                  │                   │
       └──────────────────┼───────────────────┘
                          │
                          ▼
              ┌────────────────────┐
              │  RequestLogger     │  (Logs requests/responses)
              │    (Decorator)     │
              └─────────┬──────────┘
                        │
                        ▼
              ┌────────────────────┐
              │HttpExceptionHandler│  (Converts HTTP errors)
              │    (Decorator)     │
              └─────────┬──────────┘
                        │
                        ▼
              ┌────────────────────┐
              │    HttpClient      │  (Makes HTTP requests)
              │  (Base Client)     │
              └────────────────────┘

```

## Service Organization

### KycService (Authentication & Registration)
```
KycService
├── AuthenticationEndpoint
│   ├── authenticate()
│   ├── getAccountInfo()
│   ├── searchCompanies()
│   ├── registerPeppol()
│   ├── unregisterPeppol()
│   └── getSignedContract()
├── RegistrationEndpoint
│   ├── getCompany()
│   ├── confirmCompany()
│   ├── verifyToken()
│   ├── prepareSigning()
│   ├── getContract()
│   └── finalizeSigning()
└── PasswordEndpoint
    ├── forgot()
    ├── reset()
    └── change()
```

### ProxyService (Document Transmission)
```
ProxyService
├── ProxyDocumentsEndpoint
│   ├── getAllNew()
│   ├── getStatusUpdates()
│   ├── get()
│   ├── create()
│   ├── update()
│   ├── reschedule()
│   ├── markDownloaded()
│   ├── markDownloadedBatch()
│   └── delete()
├── RegistryEndpoint
│   ├── get()
│   ├── register()
│   ├── unregister()
│   └── delete()
└── MonitorEndpoint
    ├── healthCheck()
    └── topUpBalance()
```

### AppService (Application Management)
```
AppService
├── DocumentsEndpoint
│   ├── validate()
│   ├── list()
│   ├── get()
│   ├── create()
│   ├── update()
│   ├── send()
│   ├── markRead()
│   ├── markPaid()
│   └── delete()
├── CompanyEndpoint
│   ├── get()
│   └── update()
├── PartnersEndpoint
│   ├── list()
│   ├── search()
│   ├── create()
│   ├── update()
│   └── delete()
├── ProductsEndpoint
│   ├── list()
│   ├── create()
│   ├── update()
│   └── delete()
├── ProductCategoriesEndpoint
│   ├── listRoot()
│   ├── listAll()
│   ├── get()
│   ├── create()
│   ├── update()
│   └── delete()
├── StatisticsEndpoint
│   ├── getDonation()
│   └── getAccount()
└── PeppolDirectoryEndpoint
    └── search()
```

## Request Flow

```
User Code
   │
   ▼
client.app().documents().list()
   │
   ▼
DocumentsEndpoint.list()
   │
   ▼
$this->request(RequestMethod::GET, '/sapi/document')
   │
   ▼
BaseEndpoint.request()
   │
   ▼
ClientInterface.request()
   │
   ▼
RequestLogger (logs request)
   │
   ▼
HttpExceptionHandler (wraps errors)
   │
   ▼
HttpClient (makes HTTP call)
   │
   ▼
Laravel HTTP Client
   │
   ▼
LetsPeppol API
   │
   ▼
Response flows back through decorators
   │
   ▼
User Code receives result
```

## Design Patterns Applied

### 1. Decorator Pattern
- **Purpose**: Add cross-cutting concerns without modifying core logic
- **Implementation**: RequestLogger and HttpExceptionHandler wrap HttpClient
- **Benefits**: Easy to add/remove logging, error handling, caching, etc.

### 2. Strategy Pattern (via Enums)
- **Purpose**: Encapsulate HTTP methods as first-class citizens
- **Implementation**: RequestMethod enum
- **Benefits**: Type safety, IDE autocomplete, no string literals

### 3. Facade Pattern
- **Purpose**: Provide simple interface to complex subsystem
- **Implementation**: LetsPeppolClient provides unified access to all services
- **Benefits**: Easy to use, hides complexity

### 4. Dependency Injection
- **Purpose**: Decouple components for testability
- **Implementation**: Endpoints receive ClientInterface in constructor
- **Benefits**: Easy to mock in tests

### 5. Interface Segregation
- **Purpose**: Small, focused interfaces
- **Implementation**: ClientInterface with minimal methods
- **Benefits**: Easy to implement, test, and maintain

## Exception Hierarchy

```
\RuntimeException
   │
   ▼
ApiException (base for all API errors)
   │
   ├── AuthenticationException (401)
   ├── NotFoundException (404)
   └── ValidationException (422)
```

## Configuration

```php
// config/services.php
'letspeppol' => [
    'kyc_url' => env('LETSPEPPOL_KYC_URL', 'https://kyc.letspeppol.org'),
    'proxy_url' => env('LETSPEPPOL_PROXY_URL', 'https://proxy.letspeppol.org'),
    'app_url' => env('LETSPEPPOL_APP_URL', 'https://app.letspeppol.org'),
],
```

## Testing Strategy

### Unit Tests
- Mock ClientInterface
- Test each endpoint in isolation
- Test decorators independently

### Integration Tests
- Use HTTP fake/mock
- Test full request flow
- Test error handling

### Example Test
```php
public function test_list_documents(): void
{
    $mockClient = Mockery::mock(ClientInterface::class);
    $mockClient->shouldReceive('request')
        ->with(RequestMethod::GET, '/sapi/document', [], ['page' => 0, 'size' => 20], [])
        ->andReturn(['content' => []]);

    $endpoint = new DocumentsEndpoint($mockClient);
    $result = $endpoint->list();

    $this->assertIsArray($result);
}
```
