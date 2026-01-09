<?php

namespace App\Services\LetsPeppol\Endpoints\Proxy;

use App\Services\LetsPeppol\Endpoints\BaseEndpoint;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Proxy Documents endpoint client (ProxyService)
 * 
 * Namespace: Proxy
 * Base URL: /sapi/document
 */
class DocumentsEndpoint extends BaseEndpoint
{
    /**
     * Get all new documents
     * 
     * Response:
     * [
     *   {
     *     "id": "uuid",
     *     "documentType": "INVOICE",
     *     "direction": "INCOMING",
     *     "counterPartyName": "Supplier Company",
     *     "counterPartyId": "0208:BE0987654321",
     *     "amount": 100.00,
     *     "currency": "EUR",
     *     "receivedAt": "2024-01-01T00:00:00Z"
     *   }
     * ]
     */
    public function getAllNew(int $size = 100): array
    {
        return $this->request(
            RequestMethod::GET,
            '/sapi/document',
            [],
            ['size' => $size]
        );
    }

    /**
     * Get status updates for specific documents
     * 
     * Request:
     * ["uuid-1", "uuid-2", "uuid-3"]
     * 
     * Response:
     * [
     *   {
     *     "id": "uuid-1",
     *     "status": "DELIVERED",
     *     "updatedAt": "2024-01-01T00:00:00Z"
     *   },
     *   {
     *     "id": "uuid-2",
     *     "status": "FAILED",
     *     "error": "Recipient not found"
     *   }
     * ]
     */
    public function getStatusUpdates(array $documentIds): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/document/status',
            $documentIds
        );
    }

    /**
     * Get document by ID
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "documentType": "INVOICE",
     *   "direction": "INCOMING",
     *   "counterPartyName": "Supplier Company",
     *   "ublXml": "<Invoice>...</Invoice>",
     *   "metadata": {...}
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
     * Create document to send
     * 
     * Request:
     * {
     *   "recipientId": "0208:BE0987654321",
     *   "documentType": "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2",
     *   "ublXml": "<Invoice>...</Invoice>"
     * }
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "status": "QUEUED",
     *   "createdAt": "2024-01-01T00:00:00Z"
     * }
     */
    public function create(array $documentData, bool $noArchive = false): array
    {
        return $this->request(
            RequestMethod::POST,
            '/sapi/document',
            $documentData,
            ['noArchive' => $noArchive ? 'true' : 'false']
        );
    }

    /**
     * Update document
     * 
     * Request:
     * {
     *   "ublXml": "<Invoice>...</Invoice>"
     * }
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "status": "UPDATED",
     *   "updatedAt": "2024-01-01T00:00:00Z"
     * }
     */
    public function update(string $id, array $documentData, bool $noArchive = false): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}",
            $documentData,
            ['noArchive' => $noArchive ? 'true' : 'false']
        );
    }

    /**
     * Reschedule document sending
     * 
     * Request:
     * {
     *   "scheduledAt": "2024-01-02T00:00:00Z"
     * }
     * 
     * Response:
     * {
     *   "id": "uuid",
     *   "status": "RESCHEDULED",
     *   "scheduledAt": "2024-01-02T00:00:00Z"
     * }
     */
    public function reschedule(string $id, array $documentData): array
    {
        return $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}/send",
            $documentData
        );
    }

    /**
     * Mark document as downloaded
     * 
     * Response: No content (204)
     */
    public function markDownloaded(string $id, bool $noArchive = false): void
    {
        $this->request(
            RequestMethod::PUT,
            "/sapi/document/{$id}/downloaded",
            [],
            ['noArchive' => $noArchive ? 'true' : 'false']
        );
    }

    /**
     * Mark multiple documents as downloaded
     * 
     * Request:
     * ["uuid-1", "uuid-2", "uuid-3"]
     * 
     * Response: No content (204)
     */
    public function markDownloadedBatch(array $documentIds, bool $noArchive = false): void
    {
        $this->request(
            RequestMethod::PUT,
            '/sapi/document/downloaded',
            $documentIds,
            ['noArchive' => $noArchive ? 'true' : 'false']
        );
    }

    /**
     * Cancel/delete document
     * 
     * Response: No content (204)
     */
    public function delete(string $id, bool $noArchive = false): void
    {
        $this->request(
            RequestMethod::DELETE,
            "/sapi/document/{$id}",
            [],
            ['noArchive' => $noArchive ? 'true' : 'false']
        );
    }
}
