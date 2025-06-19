<?php

namespace App\Tests\Entity;

use App\Entity\ApiCall;
use PHPUnit\Framework\TestCase;

class ApiCallTest extends TestCase
{
    private ApiCall $apiCall;

    protected function setUp(): void
    {
        $this->apiCall = new ApiCall();
    }

    public function testApiCallCreation(): void
    {
        $this->assertInstanceOf(ApiCall::class, $this->apiCall);
        $this->assertNotNull($this->apiCall->getCreatedAt());
    }

    public function testEndpointGetterAndSetter(): void
    {
        $endpoint = '/api/contacts';
        $this->apiCall->setEndpoint($endpoint);
        
        $this->assertEquals($endpoint, $this->apiCall->getEndpoint());
    }

    public function testMethodGetterAndSetter(): void
    {
        $method = 'POST';
        $this->apiCall->setMethod($method);
        
        $this->assertEquals($method, $this->apiCall->getMethod());
    }

    public function testStatusCodeGetterAndSetter(): void
    {
        $statusCode = 200;
        $this->apiCall->setStatusCode($statusCode);
        
        $this->assertEquals($statusCode, $this->apiCall->getStatusCode());
    }

    public function testUserAgentGetterAndSetter(): void
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->apiCall->setUserAgent($userAgent);
        
        $this->assertEquals($userAgent, $this->apiCall->getUserAgent());
    }

    public function testUserAgentCanBeNull(): void
    {
        $this->apiCall->setUserAgent(null);
        
        $this->assertNull($this->apiCall->getUserAgent());
    }

    public function testIpAddressGetterAndSetter(): void
    {
        $ipAddress = '192.168.1.1';
        $this->apiCall->setIpAddress($ipAddress);
        
        $this->assertEquals($ipAddress, $this->apiCall->getIpAddress());
    }

    public function testIpAddressCanBeNull(): void
    {
        $this->apiCall->setIpAddress(null);
        
        $this->assertNull($this->apiCall->getIpAddress());
    }

    public function testCreatedAtGetterAndSetter(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->apiCall->setCreatedAt($createdAt);
        
        $this->assertEquals($createdAt, $this->apiCall->getCreatedAt());
    }

    public function testResponseTimeGetterAndSetter(): void
    {
        $responseTime = 150.5; // milliseconds
        $this->apiCall->setResponseTime($responseTime);
        
        $this->assertEquals($responseTime, $this->apiCall->getResponseTime());
    }

    public function testResponseTimeCanBeNull(): void
    {
        $this->apiCall->setResponseTime(null);
        
        $this->assertNull($this->apiCall->getResponseTime());
    }

    public function testUserIdentifierGetterAndSetter(): void
    {
        $userIdentifier = 'user@example.com';
        $this->apiCall->setUserIdentifier($userIdentifier);
        
        $this->assertEquals($userIdentifier, $this->apiCall->getUserIdentifier());
    }

    public function testUserIdentifierCanBeNull(): void
    {
        $this->apiCall->setUserIdentifier(null);
        
        $this->assertNull($this->apiCall->getUserIdentifier());
    }

    public function testIdGetter(): void
    {
        // ID should be null for new entities
        $this->assertNull($this->apiCall->getId());
    }

    public function testCompleteApiCall(): void
    {
        $endpoint = '/api/contacts/1';
        $method = 'GET';
        $statusCode = 200;
        $userAgent = 'PostmanRuntime/7.29.0';
        $ipAddress = '127.0.0.1';
        $createdAt = new \DateTimeImmutable('2023-01-15 14:30:00');
        $responseTime = 45.2;
        $userIdentifier = 'admin@srsbsns.com';

        $this->apiCall->setEndpoint($endpoint);
        $this->apiCall->setMethod($method);
        $this->apiCall->setStatusCode($statusCode);
        $this->apiCall->setUserAgent($userAgent);
        $this->apiCall->setIpAddress($ipAddress);
        $this->apiCall->setCreatedAt($createdAt);
        $this->apiCall->setResponseTime($responseTime);
        $this->apiCall->setUserIdentifier($userIdentifier);

        $this->assertEquals($endpoint, $this->apiCall->getEndpoint());
        $this->assertEquals($method, $this->apiCall->getMethod());
        $this->assertEquals($statusCode, $this->apiCall->getStatusCode());
        $this->assertEquals($userAgent, $this->apiCall->getUserAgent());
        $this->assertEquals($ipAddress, $this->apiCall->getIpAddress());
        $this->assertEquals($createdAt, $this->apiCall->getCreatedAt());
        $this->assertEquals($responseTime, $this->apiCall->getResponseTime());
        $this->assertEquals($userIdentifier, $this->apiCall->getUserIdentifier());
    }

    public function testDefaultCreatedAtOnConstruction(): void
    {
        $apiCall = new ApiCall();
        
        $this->assertInstanceOf(\DateTimeImmutable::class, $apiCall->getCreatedAt());
        $this->assertGreaterThanOrEqual(
            new \DateTimeImmutable('-1 second'),
            $apiCall->getCreatedAt()
        );
    }
} 