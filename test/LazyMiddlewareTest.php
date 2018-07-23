<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aidphp\Framework\LazyMiddleware;
use Error;

class LazyMiddlewareTest extends TestCase
{
    const NAME = 'id';

    protected $dic;
    protected $req;
    protected $handler;

    public function setUp()
    {
        $this->dic = $this->createMock(ContainerInterface::class);
        $this->req = $this->createMock(ServerRequestInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    public function testProcessWithMiddleware()
    {
        $response = $this->createMock(ResponseInterface::class);

        $mid = $this->createMock(MiddlewareInterface::class);
        $mid->expects($this->once())
            ->method('process')
            ->with($this->req, $this->handler)
            ->willReturn($response);

        $this->dic->expects($this->once())
            ->method('get')
            ->with(self::NAME)
            ->willReturn($mid);

        $middleware = new LazyMiddleware($this->dic, self::NAME);
        $this->assertSame($response, $middleware->process($this->req, $this->handler));
    }

    public function testProcessWithHandler()
    {
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->willReturn($response);

        $this->dic->expects($this->once())
            ->method('get')
            ->with(self::NAME)
            ->willReturn($handler);

        $middleware = new LazyMiddleware($this->dic, self::NAME);
        $this->assertSame($response, $middleware->process($this->req, $this->handler));
    }

    public function testInvalidMiddleware()
    {
        $this->expectException(Error::class);

        $middleware = new LazyMiddleware($this->dic, self::NAME);
        $middleware->process($this->req, $this->handler);
    }
}