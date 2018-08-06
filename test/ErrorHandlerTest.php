<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Interop\Renderer\RendererInterface;
use Aidphp\Framework\ErrorHandler;
use Aidphp\Framework\ErrorInfo;
use Throwable;

class ErrorHandlerTest extends TestCase
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
            ->with(500)
            ->willReturn($this->res);
    }

    public function testHandleError()
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
            ->with(
                ErrorHandler::TEMPLATE,
                $this->callback(function ($subject) {
                    return isset($subject['error']) && $subject['error'] instanceof ErrorInfo;
                })
            )
            ->willReturn($content);

        $handler = new ErrorHandler($this->factory, $renderer);
        $this->assertSame($this->res, $handler->handleError($this->createMock(Throwable::class)));
    }
}