<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Aidphp\Framework\AbstractModule;
use Psr\Container\ContainerInterface;
use Aidphp\Di\CompositeContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AbstractModuleTest extends TestCase
{
    protected $req;
    protected $res;
    protected $handler;

    public function setUp()
    {
        $this->req = $this->createMock(ServerRequestInterface::class);
        $this->res = $this->createMock(ResponseInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    public function testProcess()
    {
        $composite = $this->createMock(CompositeContainerInterface::class);

        $module = $this->getMockBuilder(AbstractModule::class)
            ->setConstructorArgs([$composite])
            ->getMockForAbstractClass();

        $module->expects($this->once())
            ->method('createContainer')
            ->willReturn($this->createMock(ContainerInterface::class));

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->expects($this->once())
            ->method('process')
            ->with($this->req, $this->handler)
            ->willReturn($this->res);

        $module->expects($this->once())
            ->method('createPipeline')
            ->with($composite)
            ->willReturn($middleware);

        $this->assertSame($this->res, $module->process($this->req, $this->handler));
    }
}