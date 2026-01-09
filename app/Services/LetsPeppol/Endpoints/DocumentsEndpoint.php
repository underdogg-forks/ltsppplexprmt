<?php

namespace App\Services\LetsPeppol\Endpoints;

use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Documents endpoint client
 */
class DocumentsEndpoint extends BaseEndpoint
{
    /**
     * Validate UBL XML
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
     * Create document from UBL XML
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
