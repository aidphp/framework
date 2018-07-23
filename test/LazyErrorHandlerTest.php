<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Aidphp\Error\ErrorHandlerInterface;
use Aidphp\Framework\LazyErrorHandler;
use Error;

class LazyErrorHandlerTest extends TestCase
{
    protected $dic;
    protected $res;
    protected $exp;

    public function setUp()
    {
        $this->dic = $this->createMock(ContainerInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);
        $this->exp = $this->createMock(Throwable::class);
    }

    public function testHandleError()
    {
        $handler = $this->createMock(ErrorHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handleError')
            ->with($this->exp, null)
            ->willReturn($this->res);

        $this->dic->expects($this->once())
            ->method('get')
            ->with(ErrorHandlerInterface::class)
            ->willReturn($handler);

        $errorHandler = new LazyErrorHandler($this->dic);
        $this->assertSame($this->res, $errorHandler->handleError($this->exp, null));
    }

    public function testHandleErrorWithRequest()
    {
        $req = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(ErrorHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handleError')
            ->with($this->exp, $req)
            ->willReturn($this->res);

        $this->dic->expects($this->once())
            ->method('get')
            ->with(ErrorHandlerInterface::class)
            ->willReturn($handler);

        $errorHandler = new LazyErrorHandler($this->dic);
        $this->assertSame($this->res, $errorHandler->handleError($this->exp, $req));
    }

    public function testInvalidMiddleware()
    {
        $this->expectException(Error::class);

        $errorHandler = new LazyErrorHandler($this->dic);
        $errorHandler->handleError($this->exp);
    }
}