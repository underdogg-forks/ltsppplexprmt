<?php

namespace App\Services\LetsPeppol\Testing;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Enums\RequestMethod;

/**
 * Fake client for testing
 * 
 * Provides a test double that records requests and allows setting responses
 * without requiring actual HTTP calls or mocking frameworks.
 */
class FakeClient implements ClientInterface
{
    protected array $requests = [];
    protected array $responses = [];
    protected ?string $token = null;
    protected int $responseIndex = 0;

    /**
     * Queue a response to be returned
     */
    public function queueResponse(mixed $response): static
    {
        $this->responses[] = $response;
        return $this;
    }

    /**
     * Queue multiple responses
     */
    public function queueResponses(array $responses): static
    {
        foreach ($responses as $response) {
            $this->queueResponse($response);
        }
        return $this;
    }

    public function request(
        RequestMethod $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        array $headers = []
    ): mixed {
        // Record the request
        $this->requests[] = [
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data,
            'queryParams' => $queryParams,
            'headers' => $headers,
        ];

        // Return queued response or empty array
        if (isset($this->responses[$this->responseIndex])) {
            return $this->responses[$this->responseIndex++];
        }

        return [];
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Assert that a request was sent
     */
    public function assertRequestSent(
        string $endpoint,
        ?RequestMethod $method = null
    ): void {
        foreach ($this->requests as $request) {
            if ($request['endpoint'] === $endpoint) {
                if ($method === null || $request['method'] === $method) {
                    return;
                }
            }
        }

        $methodStr = $method ? " with method {$method->value}" : '';
        throw new \PHPUnit\Framework\AssertionFailedError(
            "Failed asserting that request to '{$endpoint}'{$methodStr} was sent."
        );
    }

    /**
     * Assert that a request was sent with specific data
     */
    public function assertRequestSentWithData(
        string $endpoint,
        array $expectedData,
        ?RequestMethod $method = null
    ): void {
        foreach ($this->requests as $request) {
            if ($request['endpoint'] === $endpoint) {
                if ($method === null || $request['method'] === $method) {
                    if ($request['data'] === $expectedData) {
                        return;
                    }
                }
            }
        }

        throw new \PHPUnit\Framework\AssertionFailedError(
            "Failed asserting that request to '{$endpoint}' was sent with expected data."
        );
    }

    /**
     * Assert number of requests sent
     */
    public function assertRequestCount(int $expected): void
    {
        $actual = count($this->requests);
        if ($actual !== $expected) {
            throw new \PHPUnit\Framework\AssertionFailedError(
                "Failed asserting that {$expected} requests were sent. Actually sent {$actual}."
            );
        }
    }

    /**
     * Get all recorded requests
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * Clear all recorded requests and responses
     */
    public function reset(): void
    {
        $this->requests = [];
        $this->responses = [];
        $this->responseIndex = 0;
    }
}
