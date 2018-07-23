<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Aidphp\Framework\NotAllowedMiddleware;
use Aidphp\Routing\Middleware\MethodMiddleware;
use Interop\Renderer\RendererInterface;
use Psr\Http\Message\StreamInterface;

class NotAllowedMiddlewareTest extends TestCase
{
    protected $req;
    protected $res;
    protected $factory;
    protected $handler;

    public function setUp()
    {
        $this->req = $this->createMock(ServerRequestInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);
        $this->factory = $this->createMock(ResponseFactoryInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    public function testHandle()
    {
        $this->req->expects($this->once())
            ->method('getAttribute')
            ->with(MethodMiddleware::class)
            ->willReturn(null);

        $this->factory->expects($this->never())
            ->method('createResponse');

        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->req)
            ->willReturn($this->res);

        $middleware = new NotAllowedMiddleware($this->factory, $this->createMock(RendererInterface::class));
        $this->assertSame($this->res, $middleware->process($this->req, $this->handler));
    }

    public function testHandleNotAllowed()
    {
        $content = 'content';
        $method  = 'PUT';

        $this->req->expects($this->once())
            ->method('getAttribute')
            ->with(MethodMiddleware::class)
            ->willReturn(['GET', 'POST']);

        $this->req->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $this->factory->expects($this->once())
            ->method('createResponse')
            ->with(405)
            ->willReturn($this->res);

        $newRes = $this->createMock(ResponseInterface::class);

        $this->res->expects($this->once())
            ->method('withHeader')
            ->with('Allow', 'GET,POST')
            ->willReturn($newRes);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($content)
            ->willReturn(strlen($content));

        $newRes->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $renderer = $this->createMock(RendererInterface::class);
        $renderer->expects($this->once())
            ->method('render')
            ->with(NotAllowedMiddleware::TEMPLATE, ['method' => $method])
            ->willReturn($content);

        $middleware = new NotAllowedMiddleware($this->factory, $renderer);
        $this->assertSame($newRes, $middleware->process($this->req, $this->handler));
    }
}