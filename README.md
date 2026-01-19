# LetsPeppol API Client - Test Project

---

## Introduction

This is a **test project** designed to consume and demonstrate the [LetsPeppol API](https://letspeppol.org) - a free, open-source Peppol e-invoicing platform. The project showcases a modern, well-architected API client implementation following enterprise-grade design patterns and SOLID principles.

### Purpose

This project serves as:
- **Testing Ground** for LetsPeppol API integration
- **Reference Implementation** of clean API client architecture
- **Learning Resource** demonstrating design patterns and best practices
- **Development Template** for building production-ready API clients

### Key Features

- ✅ **Complete API Coverage** - All LetsPeppol endpoints (KYC, Proxy, App services)
- ✅ **Modern Architecture** - Decorator pattern, endpoint-specific clients, namespaced organization
- ✅ **Type Safety** - RequestMethod enum, strict typing, domain-specific exceptions
- ✅ **Fully Testable** - Dependency injection, mock-friendly interfaces, comprehensive test suite
- ✅ **SOLID Principles** - Single responsibility, interface segregation, dependency inversion
- ✅ **Production Ready** - Logging, error handling, retry logic, pagination helpers

---

## Quick Start

### Installation

```bash
# Clone the repository
git clone https://github.com/underdogg-forks/ltsppplexprmt
cd ltsppplexprmt

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure LetsPeppol credentials in .env
LETSPEPPOL_KYC_URL=https://kyc.letspeppol.org
LETSPEPPOL_PROXY_URL=https://proxy.letspeppol.org
LETSPEPPOL_APP_URL=https://app.letspeppol.org
```

### Basic Usage

```php
use App\Services\LetsPeppol\LetsPeppolClient;

// Create client
$client = new LetsPeppolClient();

// Authenticate with LetsPeppol
$token = $client->authenticate('user@example.com', 'password');

// Use the API
$documents = $client->app()->documents()->list();
$company = $client->app()->company()->get();
$partners = $client->app()->partners()->list();
```

---

## LetsPeppol API Client Architecture

### What is LetsPeppol?

[LetsPeppol](https://letspeppol.org) is a free, open-source platform that enables businesses to send and receive e-invoices through the Peppol network. The platform provides three main API modules:

1. **KYC Service** - Authentication, registration, and company verification
2. **Proxy Service** - Document transmission and Peppol network access
3. **App Service** - Document management, partners, products, and statistics

### Client Architecture

This project implements a production-grade API client with the following architecture:

#### Decorator Pattern Chain
```
RequestLogger → HttpExceptionHandler → HttpClient
```
- **RequestLogger** - Automatic logging with timing information
- **HttpExceptionHandler** - Converts HTTP errors to domain-specific exceptions
- **HttpClient** - Base HTTP communication layer

#### Namespaced Endpoint Organization
```
Endpoints/
├── App/          (Application Service)
│   ├── CompanyEndpoint
│   ├── DocumentsEndpoint
│   ├── PartnersEndpoint
│   ├── ProductsEndpoint
│   ├── ProductCategoriesEndpoint
│   ├── StatisticsEndpoint
│   └── PeppolDirectoryEndpoint
├── Kyc/          (KYC Service)
│   ├── AuthenticationEndpoint
│   ├── RegistrationEndpoint
│   └── PasswordEndpoint
└── Proxy/        (Proxy Service)
    ├── DocumentsEndpoint
    ├── RegistryEndpoint
    └── MonitorEndpoint
```

### Key Design Patterns

- **Decorator Pattern** - Cross-cutting concerns (logging, error handling)
- **Strategy Pattern** - RequestMethod enum for HTTP methods
- **Facade Pattern** - LetsPeppolClient as simple entry point
- **Dependency Injection** - Testable, loosely coupled components
- **Single Responsibility** - Each endpoint handles one resource

---

## Usage Examples

### Working with Documents

```php
// Validate UBL XML before sending
$ublXml = file_get_contents('invoice.xml');
$validation = $client->app()->documents()->validate($ublXml);

if ($validation['valid']) {
    // Create document as draft
    $document = $client->app()->documents()->create($ublXml, draft: true);
    
    // Review and send
    $client->app()->documents()->send($document['id']);
    
    // List sent documents
    $sent = $client->app()->documents()->list([
        'type' => 'INVOICE',
        'direction' => 'OUTGOING'
    ]);
}

// Loop through all documents with pagination
$client->app()->documents()->listAll(function($documents) {
    foreach ($documents as $doc) {
        echo "Document {$doc['id']}: {$doc['documentNumber']}\n";
    }
}, ['type' => 'INVOICE'], pageSize: 50);
```

### Receiving Documents via Proxy

```php
// Get newly received documents
$newDocs = $client->proxy()->documents()->getAllNew(100);

foreach ($newDocs as $doc) {
    // Download and process the document
    processInvoice($doc);
    
    // Mark as downloaded so it won't appear in future calls
    $client->proxy()->documents()->markDownloaded($doc['id']);
}
```

### Managing Partners

```php
// Search for partner by Peppol ID
$peppolId = '0208:BE0987654321';
$results = $client->app()->partners()->search($peppolId);

if (empty($results)) {
    // Create new partner
    $partner = $client->app()->partners()->create([
        'peppolId' => $peppolId,
        'name' => 'Acme Corp BVBA',
        'vatNumber' => 'BE0987654321',
        'email' => 'invoices@acme.com',
    ]);
}
```

### Error Handling

```php
use App\Services\LetsPeppol\Exceptions\{
    AuthenticationException,
    NotFoundException,
    ValidationException
};

try {
    $document = $client->app()->documents()->get($id);
} catch (AuthenticationException $e) {
    // Token expired - re-authenticate
    $client->authenticate($email, $password);
    retry();
} catch (NotFoundException $e) {
    // Document doesn't exist
    logError("Document {$id} not found");
} catch (ValidationException $e) {
    // Invalid request data
    showUserError($e->getMessage());
}
```

---

## Testing

The API client is fully testable with comprehensive PHPUnit tests:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter LetsPeppol
```

### Writing Tests

Tests follow conventions:
- Methods start with `it_` and make grammatical sense
- Use PHP 8 `#[Test]` attributes
- Follow "Arrange, Act, Assert" pattern
- Use fakes over mocks for better maintainability

```php
use App\Services\LetsPeppol\Testing\FakeClient;

#[Test]
public function it_lists_documents_with_filters(): void
{
    // Arrange
    $fake = new FakeClient();
    $endpoint = new DocumentsEndpoint($fake);
    
    // Act
    $result = $endpoint->list(['type' => 'INVOICE']);
    
    // Assert
    $this->assertIsArray($result);
    $fake->assertRequestSent('/sapi/document');
}
```

---

## Documentation

- 📖 [API Client README](app/Services/LetsPeppol/README.md) - Detailed API reference
- 🏗️ [Architecture Overview](docs/architecture.md) - System design and patterns
- 📋 [Guidelines](.junie/guidelines.md) - Development standards and best practices
- 🤖 [Copilot Instructions](.github/copilot-instructions.md) - AI-assisted development guide
- 🚀 [Quick Start Guide](docs/api/LETSPEPPOL-QUICKSTART.md) - Get started quickly

---

## Project Structure

This Laravel-based test project includes:

- **API Client** (`app/Services/LetsPeppol/`) - Complete LetsPeppol integration
- **Tests** (`tests/Unit/Services/LetsPeppol/`) - Comprehensive test suite
- **Documentation** (`docs/`) - Architecture and usage guides
- **Examples** (`app/Services/LetsPeppol/Examples/`) - Working code samples

### Authentication Features

The base Laravel application includes:
- Login / Registration
- Password Reset Flow
- Email Confirmation
- Dashboard and Profile Management

---

## How to use it?

To use this kit, you can install it using:

```bash
laravel new --using=laraveldaily/starter-kit
```

From there, you can modify the kit to your needs.

---

## Design Elements

If you want to see examples of what design elements we have, you can [visit the Wiki](<https://github.com/LaravelDaily/starter-kit/wiki/Design-Examples-(Raw-Files)>) and see the raw HTML files.

---

## Licence

Starter kit is open-sourced software licensed under the MIT license.
