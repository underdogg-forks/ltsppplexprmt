<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Proxy Documents endpoint client
 */
class ProxyDocumentsEndpoint extends BaseEndpoint
{
    /**
     * Get all new documents
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
