<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Aidphp\Error\ErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Aidphp\Framework\Server;
use Aidphp\Framework\EmitterInterface;

class ServerTest extends TestCase
{
    protected $res;
    protected $errorHandler;
    protected $handler;
    protected $emitter;

    public function setUp()
    {
        $this->res = $this->createMock(ResponseInterface::class);
        $this->errorHandler = $this->createMock(ErrorHandlerInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
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

        $server = new Server($this->handler, $this->errorHandler, $this->emitter);
        $this->assertNull($server->run());
    }

    public function testHandleWithException()
    {
        $save = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'INVALID METHOD';

        $this->handler->expects($this->never())
            ->method('handle');

        $this->errorHandler->expects($this->once())
            ->method('handleError')
            ->willReturn($this->res);

        $server = new Server($this->handler, $this->errorHandler, $this->emitter);
        $this->assertNull($server->run());

        $_SERVER = $save;
    }
}