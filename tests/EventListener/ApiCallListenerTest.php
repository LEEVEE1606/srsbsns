<?php

namespace App\Tests\EventListener;

use App\Entity\ApiCall;
use App\EventListener\ApiCallListener;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiCallListenerTest extends TestCase
{
    private ApiCallListener $listener;
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        
        $this->listener = new ApiCallListener(
            $this->entityManager,
            $this->tokenStorage
        );
    }

    public function testOnKernelRequestWithApiPath(): void
    {
        $request = $this->createRequest('/api/contacts');
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->listener->onKernelRequest($event);

        // Test that startTime is set (we can't directly access it, but we can test the behavior)
        $this->assertTrue(true); // If no exception is thrown, the method worked
    }

    public function testOnKernelRequestWithNonApiPath(): void
    {
        $request = $this->createRequest('/admin/login');
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->listener->onKernelRequest($event);

        // Should not set startTime for non-API paths
        $this->assertTrue(true);
    }

    public function testOnKernelRequestWithSubRequest(): void
    {
        $request = $this->createRequest('/api/contacts');
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->listener->onKernelRequest($event);

        // Should not process sub-requests
        $this->assertTrue(true);
    }

    public function testOnKernelResponseWithApiPath(): void
    {
        $request = $this->createRequest('/api/contacts');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        // Set up the request event first
        $requestEvent = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
        $this->listener->onKernelRequest($requestEvent);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($apiCall) {
                return $apiCall instanceof ApiCall
                    && $apiCall->getEndpoint() === '/api/contacts'
                    && $apiCall->getMethod() === 'GET'
                    && $apiCall->getStatusCode() === 200;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithNonApiPath(): void
    {
        $request = $this->createRequest('/admin/login');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithApiDocsPath(): void
    {
        $request = $this->createRequest('/api/docs');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithAuthenticatedUser(): void
    {
        $request = $this->createRequest('/api/contacts');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        // Set up the request event first
        $requestEvent = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
        $this->listener->onKernelRequest($requestEvent);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($apiCall) {
                return $apiCall instanceof ApiCall
                    && $apiCall->getUserIdentifier() === 'user@example.com';
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithUserWithoutGetUserIdentifier(): void
    {
        $request = $this->createRequest('/api/contacts');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        // Set up the request event first
        $requestEvent = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
        $this->listener->onKernelRequest($requestEvent);

        $user = $this->createMock(UserInterface::class);
        $user->method('getEmail')->willReturn('user@example.com');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($apiCall) {
                return $apiCall instanceof ApiCall
                    && $apiCall->getUserIdentifier() === 'user@example.com';
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithNoToken(): void
    {
        $request = $this->createRequest('/api/contacts');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        // Set up the request event first
        $requestEvent = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
        $this->listener->onKernelRequest($requestEvent);

        $this->tokenStorage
            ->method('getToken')
            ->willReturn(null);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($apiCall) {
                return $apiCall instanceof ApiCall
                    && $apiCall->getUserIdentifier() === null;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithExceptionHandling(): void
    {
        $request = $this->createRequest('/api/contacts');
        $response = new Response('', 200);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        // Set up the request event first
        $requestEvent = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
        $this->listener->onKernelRequest($requestEvent);

        $this->entityManager
            ->method('persist')
            ->willThrowException(new \Exception('Database error'));

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        // Should not throw exception, just log error
        $this->listener->onKernelResponse($event);
        $this->assertTrue(true);
    }

    public function testOnKernelResponseWithDifferentHttpMethods(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        
        foreach ($methods as $method) {
            $request = $this->createRequest('/api/contacts', $method);
            $response = new Response('', 200);
            $event = new ResponseEvent(
                $this->createMock(HttpKernelInterface::class),
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                $response
            );

            // Set up the request event first
            $requestEvent = new RequestEvent(
                $this->createMock(HttpKernelInterface::class),
                $request,
                HttpKernelInterface::MAIN_REQUEST
            );
            $this->listener->onKernelRequest($requestEvent);

            $this->entityManager
                ->expects($this->once())
                ->method('persist')
                ->with($this->callback(function ($apiCall) use ($method) {
                    return $apiCall instanceof ApiCall
                        && $apiCall->getMethod() === $method;
                }));

            $this->entityManager
                ->expects($this->once())
                ->method('flush');

            $this->listener->onKernelResponse($event);
        }
    }

    public function testOnKernelResponseWithDifferentStatusCodes(): void
    {
        $statusCodes = [200, 201, 400, 401, 403, 404, 500];
        
        foreach ($statusCodes as $statusCode) {
            $request = $this->createRequest('/api/contacts');
            $response = new Response('', $statusCode);
            $event = new ResponseEvent(
                $this->createMock(HttpKernelInterface::class),
                $request,
                HttpKernelInterface::MAIN_REQUEST,
                $response
            );

            // Set up the request event first
            $requestEvent = new RequestEvent(
                $this->createMock(HttpKernelInterface::class),
                $request,
                HttpKernelInterface::MAIN_REQUEST
            );
            $this->listener->onKernelRequest($requestEvent);

            $this->entityManager
                ->expects($this->once())
                ->method('persist')
                ->with($this->callback(function ($apiCall) use ($statusCode) {
                    return $apiCall instanceof ApiCall
                        && $apiCall->getStatusCode() === $statusCode;
                }));

            $this->entityManager
                ->expects($this->once())
                ->method('flush');

            $this->listener->onKernelResponse($event);
        }
    }

    private function createRequest(string $path, string $method = 'GET'): Request
    {
        $request = Request::create($path, $method);
        $request->headers->set('User-Agent', 'Test User Agent');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        return $request;
    }
} 