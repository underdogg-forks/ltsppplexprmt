# LetsPeppol API Client

A modern, well-architected API client for the LetsPeppol e-invoicing platform.

## Features

- ✅ **Decorator Pattern** for logging and error handling
- ✅ **Endpoint-specific clients** for better organization
- ✅ **Type-safe** with RequestMethod enum and strict typing
- ✅ **Domain-specific exceptions** for better error handling
- ✅ **Fully testable** with dependency injection
- ✅ **SOLID principles** applied throughout
- ✅ **Comprehensive documentation** and examples

## Quick Start

### Basic Usage

```php
use App\Services\LetsPeppol\LetsPeppolClient;

// Create client
$client = new LetsPeppolClient();

// Authenticate
$token = $client->authenticate('user@example.com', 'password');

// Use endpoints
$documents = $client->app()->documents()->list();
$company = $client->app()->company()->get();
$partners = $client->app()->partners()->list();
```

### Working with Documents

```php
// Validate UBL XML
$ublXml = file_get_contents('invoice.xml');
$validation = $client->app()->documents()->validate($ublXml);

if ($validation['valid']) {
    // Create document
    $document = $client->app()->documents()->create($ublXml, draft: true);
    
    // Send document
    $client->app()->documents()->send($document['id']);
    
    // List documents
    $documents = $client->app()->documents()->list([
        'type' => 'INVOICE',
        'direction' => 'OUTGOING'
    ]);
}
```

### Receiving Documents (Proxy)

```php
// Get new documents
$newDocs = $client->proxy()->documents()->getAllNew(100);

foreach ($newDocs as $doc) {
    // Process document
    processDocument($doc);
    
    // Mark as downloaded
    $client->proxy()->documents()->markDownloaded($doc['id']);
}
```

### Partner Management

```php
// Search for partner
$results = $client->app()->partners()->search('0208:BE0987654321');

if (empty($results)) {
    // Create new partner
    $partner = $client->app()->partners()->create([
        'peppolId' => '0208:BE0987654321',
        'name' => 'Partner Company BVBA',
        'vatNumber' => 'BE0987654321',
        'email' => 'contact@partner.com',
    ]);
}

// List all partners
$partners = $client->app()->partners()->list();
```

### Error Handling

```php
use App\Services\LetsPeppol\Exceptions\AuthenticationException;
use App\Services\LetsPeppol\Exceptions\NotFoundException;
use App\Services\LetsPeppol\Exceptions\ValidationException;

try {
    $document = $client->app()->documents()->get($id);
} catch (AuthenticationException $e) {
    // Token expired, re-authenticate
    $client->authenticate($email, $password);
} catch (NotFoundException $e) {
    // Document not found
    echo "Document does not exist";
} catch (ValidationException $e) {
    // Validation failed
    echo "Invalid data: " . $e->getMessage();
}
```

## Architecture

### Decorator Chain

```
RequestLogger → HttpExceptionHandler → HttpClient
```

All requests flow through this chain, providing automatic logging and error handling.

### Service Organization

The client is organized into three main services:

1. **KycService** - Authentication and registration
2. **ProxyService** - Document transmission and registry
3. **AppService** - Application management

### Endpoint-Specific Clients

Each API endpoint has its own dedicated client class:

```
app/Services/LetsPeppol/Endpoints/
├── DocumentsEndpoint.php
├── CompanyEndpoint.php
├── PartnersEndpoint.php
├── ProductsEndpoint.php
├── ProductCategoriesEndpoint.php
├── StatisticsEndpoint.php
├── PeppolDirectoryEndpoint.php
├── AuthenticationEndpoint.php
├── RegistrationEndpoint.php
├── PasswordEndpoint.php
├── ProxyDocumentsEndpoint.php
├── RegistryEndpoint.php
└── MonitorEndpoint.php
```

## Available Endpoints

### KYC Service

**Authentication**
- `authenticate(email, password)` - Get JWT token
- `getAccountInfo()` - Get account details
- `searchCompanies()` - Search for companies
- `registerPeppol()` - Register on Peppol Directory
- `unregisterPeppol()` - Unregister from Peppol Directory

**Registration**
- `getCompany(peppolId)` - Get company info
- `confirmCompany(data, language)` - Confirm and send verification email
- `verifyToken(token)` - Verify email token
- `prepareSigning(data)` - Prepare document signing
- `getContract(directorId, token)` - Get contract PDF
- `finalizeSigning(data)` - Complete signing

**Password**
- `forgot(email, language)` - Request password reset
- `reset(token, newPassword)` - Reset password
- `change(oldPassword, newPassword)` - Change password

### Proxy Service

**Documents**
- `getAllNew(size)` - Get new received documents
- `getStatusUpdates(documentIds)` - Get status updates
- `get(id)` - Get document by ID
- `create(data, noArchive)` - Create document
- `update(id, data, noArchive)` - Update document
- `reschedule(id, data)` - Reschedule sending
- `markDownloaded(id)` - Mark as downloaded
- `markDownloadedBatch(ids)` - Batch mark downloaded
- `delete(id)` - Delete document

**Registry**
- `get()` - Get registry info
- `register(data)` - Register on Access Point
- `unregister()` - Unregister from Access Point
- `delete()` - Remove from registry

**Monitor**
- `healthCheck()` - Health check
- `topUpBalance(amount)` - Top up balance

### App Service

**Documents**
- `validate(ublXml)` - Validate UBL XML
- `list(filters, page, size, sort)` - List documents
- `get(id)` - Get document
- `create(ublXml, draft, schedule)` - Create document
- `update(id, ublXml, draft, schedule)` - Update document
- `send(id, schedule)` - Send document
- `markRead(id)` - Mark as read
- `markPaid(id)` - Mark as paid
- `delete(id)` - Delete document

**Company**
- `get()` - Get company info
- `update(data)` - Update company info

**Partners**
- `list()` - List partners
- `search(peppolId)` - Search partners
- `create(data)` - Create partner
- `update(id, data)` - Update partner
- `delete(id)` - Delete partner

**Products**
- `list()` - List products
- `create(data)` - Create product
- `update(id, data)` - Update product
- `delete(id)` - Delete product

**Product Categories**
- `listRoot(deep)` - List root categories
- `listAll()` - List all categories flat
- `get(id, deep)` - Get category
- `create(data)` - Create category
- `update(id, data)` - Update category
- `delete(id)` - Delete category

**Statistics**
- `getDonation()` - Get donation stats
- `getAccount()` - Get account totals

**Peppol Directory**
- `search(name, participant)` - Search directory

## Configuration

Add to your `config/services.php`:

```php
'letspeppol' => [
    'kyc_url' => env('LETSPEPPOL_KYC_URL', 'https://kyc.letspeppol.org'),
    'proxy_url' => env('LETSPEPPOL_PROXY_URL', 'https://proxy.letspeppol.org'),
    'app_url' => env('LETSPEPPOL_APP_URL', 'https://app.letspeppol.org'),
],
```

## Testing

The architecture is designed for testability. Mock the `ClientInterface`:

```php
use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Endpoints\DocumentsEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

public function test_list_documents(): void
{
    $mockClient = $this->createMock(ClientInterface::class);
    $mockClient->expects($this->once())
        ->method('request')
        ->with(RequestMethod::GET, '/sapi/document')
        ->willReturn(['content' => []]);

    $endpoint = new DocumentsEndpoint($mockClient);
    $result = $endpoint->list();

    $this->assertIsArray($result);
}
```

## Examples

See `app/Services/LetsPeppol/Examples/ExampleUsage.php` for comprehensive examples.

## Documentation

- [Architecture Overview](../../../docs/architecture.md)
- [Guidelines](.junie/guidelines.md)
- [Copilot Instructions](.github/copilot-instructions.md)

## Design Patterns

- **Decorator Pattern** - Logging and error handling
- **Strategy Pattern** - RequestMethod enum
- **Facade Pattern** - LetsPeppolClient
- **Dependency Injection** - Testable components
- **Interface Segregation** - Small, focused interfaces

## SOLID Principles

- ✅ **Single Responsibility** - Each endpoint handles one resource
- ✅ **Open/Closed** - Extend via new endpoints
- ✅ **Liskov Substitution** - All clients implement ClientInterface
- ✅ **Interface Segregation** - Minimal ClientInterface
- ✅ **Dependency Inversion** - Depend on abstractions
