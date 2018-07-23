<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Factory\ResponseFactoryInterface;
use Aidphp\Framework\NotFoundHandler;
use Interop\Renderer\RendererInterface;
use Psr\Http\Message\StreamInterface;

class NotFoundHandlerTest extends TestCase
{
    protected $req;
    protected $res;
    protected $factory;

    public function setUp()
    {
        $this->req = $this->createMock(ServerRequestInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);

        $this->factory = $this->createMock(ResponseFactoryInterface::class);
        $this->factory->expects($this->once())
            ->method('createResponse')
            ->with(404)
            ->willReturn($this->res);
    }

    public function testHandleNotFound()
    {
        $content = 'content';

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($content)
            ->willReturn(strlen($content));

       $this->res->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $renderer = $this->createMock(RendererInterface::class);
        $renderer->expects($this->once())
            ->method('render')
            ->with(NotFoundHandler::TEMPLATE)
            ->willReturn($content);

        $handler = new NotFoundHandler($this->factory, $renderer);
        $this->assertSame($this->res, $handler->handle($this->req));
    }
}