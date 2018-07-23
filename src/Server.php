<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Server\RequestHandlerInterface;
use Aidphp\Error\ErrorHandlerInterface;
use Aidphp\Http\ServerRequestFactory;
use Throwable;

class Server
{
    protected $handler;
    protected $errorHandler;
    protected $emitter;

    public function __construct(RequestHandlerInterface $handler, ErrorHandlerInterface $errorHandler, EmitterInterface $emitter)
    {
        $this->handler = $handler;
        $this->errorHandler = $errorHandler;
        $this->emitter = $emitter;
    }

    public function run(): void
    {
        try
        {
            $res = $this->handler->handle(ServerRequestFactory::createFromGlobals());
        }
        catch (Throwable $e)
        {
            $res = $this->errorHandler->handleError($e);
        }

        $this->emitter->emit($res);
    }
}