<?php

namespace App\EventListener;

use App\Entity\ApiCall;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiCallListener
{
    private $startTime;
    private $entityManager;
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Only track API calls
        if (str_starts_with($path, '/api/')) {
            $this->startTime = microtime(true);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $path = $request->getPathInfo();

        // Only track API calls
        if (!str_starts_with($path, '/api/')) {
            return;
        }

        // Skip API Platform documentation routes
        if (str_contains($path, '/docs') || str_contains($path, '/contexts')) {
            return;
        }

        $responseTime = null;
        if ($this->startTime) {
            $responseTime = (microtime(true) - $this->startTime) * 1000; // Convert to milliseconds
        }

        $apiCall = new ApiCall();
        $apiCall->setEndpoint($path);
        $apiCall->setMethod($request->getMethod());
        $apiCall->setStatusCode($response->getStatusCode());
        $apiCall->setUserAgent($request->headers->get('User-Agent'));
        $apiCall->setIpAddress($request->getClientIp());
        $apiCall->setResponseTime($responseTime);

        // Get user identifier if authenticated
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser()) {
            $user = $token->getUser();
            if (method_exists($user, 'getUserIdentifier')) {
                $apiCall->setUserIdentifier($user->getUserIdentifier());
            } elseif (method_exists($user, 'getEmail')) {
                $apiCall->setUserIdentifier($user->getEmail());
            }
        }

        try {
            $this->entityManager->persist($apiCall);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // Log error but don't break the API response
            error_log('Failed to log API call: ' . $e->getMessage());
        }
    }
} 