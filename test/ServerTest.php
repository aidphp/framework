<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Aidphp\Http\ServerRequestFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Aidphp\Error\ErrorHandlerInterface;
use Interop\Emitter\EmitterInterface;
use Aidphp\Framework\Server;
use RuntimeException;

class ServerTest extends TestCase
{
    protected $res;
    protected $requestFactory;
    protected $handler;
    protected $errorHandler;
    protected $emitter;

    public function setUp()
    {
        $this->res = $this->createMock(ResponseInterface::class);
        $this->requestFactory = $this->createMock(ServerRequestFactoryInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->errorHandler = $this->createMock(ErrorHandlerInterface::class);
        $this->emitter = $this->createMock(EmitterInterface::class);
        $this->emitter->expects($this->once())
            ->method('emit')
            ->with($this->res);
    }

    public function testHandle()
    {
        $this->handler->expects($this->once())
            ->method('handle')
            ->willReturn($this->res);

        $server = new Server($this->requestFactory, $this->handler, $this->errorHandler, $this->emitter);
        $this->assertNull($server->run());
    }

    public function testHandleWithException()
    {
        $exp = new RuntimeException();

        $this->requestFactory->expects($this->once())
            ->method('createFromGlobals')
            ->will($this->throwException($exp));

        $this->handler->expects($this->never())
            ->method('handle');

        $this->errorHandler->expects($this->once())
            ->method('handleError')
            ->with($exp)
            ->willReturn($this->res);

        $server = new Server($this->requestFactory, $this->handler, $this->errorHandler, $this->emitter);
        $this->assertNull($server->run());
    }
}