<?php

namespace Tests\Unit\Services\LetsPeppol\Decorators;

use App\Services\LetsPeppol\Contracts\ClientInterface;
use App\Services\LetsPeppol\Decorators\RequestLogger;
use App\Services\LetsPeppol\Enums\RequestMethod;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestLogger::class)]
class RequestLoggerTest extends TestCase
{
    private ClientInterface $mockClient;
    private RequestLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockClient = $this->createMock(ClientInterface::class);
        $this->logger = new RequestLogger($this->mockClient);
    }

    #[Test]
    public function it_logs_successful_api_request(): void
    {
        // Arrange
        Log::spy();
        
        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->willReturn(['success' => true]);

        // Act
        $result = $this->logger->request(
            RequestMethod::GET,
            '/test-endpoint'
        );

        // Assert
        $this->assertEquals(['success' => true], $result);
        Log::shouldHaveReceived('info')->twice();
    }

    #[Test]
    public function it_logs_failed_api_request(): void
    {
        // Arrange
        Log::spy();
        $exception = new \RuntimeException('API Error', 500);

        $this->mockClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->logger->request(RequestMethod::GET, '/test-endpoint');
        
        Log::shouldHaveReceived('info')->once();
        Log::shouldHaveReceived('error')->once();
    }

    #[Test]
    public function it_delegates_token_operations_to_wrapped_client(): void
    {
        // Arrange
        $token = 'test-token';

        $this->mockClient
            ->expects($this->once())
            ->method('setToken')
            ->with($token)
            ->willReturn($this->mockClient);

        $this->mockClient
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        // Act
        $this->logger->setToken($token);
        $result = $this->logger->getToken();

        // Assert
        $this->assertEquals($token, $result);
    }
}
