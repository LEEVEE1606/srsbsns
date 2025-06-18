<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Psr\Log\LoggerInterface;

class AuthenticationFailureListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        
        // Log the failure details
        $this->logger->error('Authentication failure: ' . $exception->getMessage(), [
            'exception' => $exception,
            'request_uri' => $request->getRequestUri(),
            'request_method' => $request->getMethod(),
            'request_content' => $request->getContent(),
            'user_identifier' => $request->request->get('email') ?? $request->request->get('username'),
        ]);
    }
} 