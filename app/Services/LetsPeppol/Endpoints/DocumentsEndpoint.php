<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Documents endpoint client (AppService)
 * 
 * Namespace: App
 * Base URL: /sapi/document
 */
class DocumentsEndpoint extends BaseEndpoint
{
    /**
     * Validate UBL XML
     * 
     * Request: UBL XML content
     * 
     * Response:
     * {
     *   "valid": true,
     *   "errors": []
     * }
     */
    public function validate(string $ublXml): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/document/validate',
            ['body' => $ublXml],
            [],
            ['Content-Type' => 'text/xml']
        );
    }

    /**
     * List documents with filtering and pagination
     * 
     * Response:
     * {
     *   "content": [
     *     {
     *       "id": "uuid",
     *       "type": "INVOICE",
     *       "direction": "OUTGOING",
     *       "draft": false,
     *       "amount": 100.00,
     *       "currency": "EUR"
     *     }
     *   ],
     *   "totalElements": 100,
     *   "totalPages": 5,
     *   "number": 0,
     *   "size": 20
     * }
     */
    public function list(array $filters = [], int $page = 0, int $size = 20, ?string $sort = null): array
    {
        $params = array_merge($filters, [
            'page' => $page,
            'size' => $size,
        ]);

        if ($sort) {
            $params['sort'] = $sort;
        }

        return $this->request(
            RequestMethod::GET,
            '/sapi/document',
            [],
            $params
        );
    }

    /**
     * Loop through all documents with pagination using do...while
     * 
     * @param callable $callback Function to call for each page of documents
     * @param array $filters Optional filters to apply
     * @param int $size Number of documents per page
     * @param string|null $sort Optional sort parameter
     */
    public function listAll(callable $callback, array $filters = [], int $size = 20, ?string $sort = null): void
    {
        $page = 0;
        
        do {
            $response = $this->list($filters, $page, $size, $sort);
            
            // Call the callback with the current page of documents
            $callback($response['content'] ?? []);
            
            $page++;
            $hasMore = !empty($response['content']) && count($response['content']) === $size;
        } while ($hasMore);
    }

    /**
     * Get document by ID
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "type": "INVOICE",
     *   "direction": "OUTGOING",
     *   "draft": false,
     *   "amount": 100.00,
     *   "currency": "EUR",
     *   "createdAt": "2024-01-01T00:00:00Z"
     * }
     */
    public function get(string $id): array
    {
        return $this->request(
            RequestMethod::GET,
            "/sapi/document/{$id}"
        );
    }

    /**
     * Create document from UBL XML
     * 
     * Request: UBL XML content
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "type": "INVOICE",
     *   "draft": true
     * }
     */
    public function create(string $ublXml, bool $draft = false, ?string $schedule = null): array
    {
        $params = ['draft' => $draft ? 'true' : 'false'];
        if ($schedule) {
            $params['schedule'] = $schedule;
        }

        return $this->request(
            RequestMethod::POST,
            '/sapi/document',
            ['body' => $ublXml],
            $params,
            ['Content-Type' => 'text/xml']
        );
    }

    /**
     * Update document
     * 
     * Request: UBL XML content
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "type": "INVOICE",
     *   "draft": true
     * }
     */
    public function update(string $id, string $ublXml, bool $draft = false, ?string $schedule = null): array
    {
        $params = ['draft' => $draft ? 'true' : 'false'];
        if ($schedule) {
            $params['schedule'] = $schedule;
        }

        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}",
            ['body' => $ublXml],
            $params,
            ['Content-Type' => 'text/xml']
        );
    }

    /**
     * Send document
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "status": "SENT"
     * }
     */
    public function send(string $id, ?string $schedule = null): array
    {
        $params = [];
        if ($schedule) {
            $params['schedule'] = $schedule;
        }

        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}/send",
            [],
            $params
        );
    }

    /**
     * Mark document as read
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "read": true
     * }
     */
    public function markRead(string $id): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}/read"
        );
    }

    /**
     * Mark document as paid
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "paid": true
     * }
     */
    public function markPaid(string $id): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}/paid"
        );
    }

    /**
     * Delete document
     */
    public function delete(string $id): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/document/{$id}"
        );
    }
}
