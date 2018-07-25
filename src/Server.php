<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Interop\Http\PhpServerRequestFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Aidphp\Error\ErrorHandlerInterface;
use Interop\Http\EmitterInterface;
use Throwable;

class Server
{
    protected $requestFactory;
    protected $handler;
    protected $errorHandler;
    protected $emitter;

    public function __construct(PhpServerRequestFactoryInterface $requestFactory, RequestHandlerInterface $handler, ErrorHandlerInterface $errorHandler, EmitterInterface $emitter)
    {
        $this->requestFactory = $requestFactory;
        $this->handler = $handler;
        $this->errorHandler = $errorHandler;
        $this->emitter = $emitter;
    }

    public function run(): void
    {
        try
        {
            $res = $this->handler->handle($this->requestFactory->createServerRequestFromGlobals());
        }
        catch (Throwable $e)
        {
            $res = $this->errorHandler->handleError($e);
        }

        $this->emitter->emit($res);
    }
}