<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Aidphp\Framework\ErrorInfo;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class ErrorInfoTest extends TestCase
{
    public function testConstructor()
    {
        $e = $this->createMock(Throwable::class);

        $errorInfo = new ErrorInfo($e);

        $this->assertFalse($errorInfo->isDebug());
        $this->assertSame('', $errorInfo->getMethod());
        $this->assertSame('', $errorInfo->getUri());
        $this->assertSame(get_class($e), $errorInfo->getType());
        $this->assertSame(0, $errorInfo->getCode());
        $this->assertSame('', $errorInfo->getMessage());
        $this->assertSame([
            'GET'     => [],
            'POST'    => [],
            'COOKIES' => [],
            'FILES'   => [],
            'SERVER'  => $_SERVER,
            'PARAMS'  => [],
        ], $errorInfo->getParameters());

        $this->assertSame($e->getFile(), $errorInfo->getFile());
        $this->assertSame($e->getLine(), $errorInfo->getLine());
        $this->assertSame($e->getTrace(), $errorInfo->getTrace());
    }

    public function testConstructorWithDebug()
    {
        $errorInfo = new ErrorInfo($this->createMock(Throwable::class), true);
        $this->assertTrue($errorInfo->isDebug());
    }

    public function testConstructorWithRequest()
    {
        $url    = '/foo';
        $method = 'GET';

        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())
            ->method('__toString')
            ->willReturn($url);

        $req = $this->createMock(ServerRequestInterface::class);
        $req->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $req->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $req->expects($this->once())->method('getQueryParams')->willReturn([]);
        $req->expects($this->once())->method('getParsedBody')->willReturn([]);
        $req->expects($this->once())->method('getCookieParams')->willReturn([]);
        $req->expects($this->once())->method('getUploadedFiles')->willReturn([]);
        $req->expects($this->once())->method('getServerParams')->willReturn([]);
        $req->expects($this->once())->method('getAttributes')->willReturn([]);

        $errorInfo = new ErrorInfo($this->createMock(Throwable::class), false, $req);
        $this->assertSame($url, $errorInfo->getUri());
        $this->assertSame($method, $errorInfo->getMethod());
        $this->assertSame([
            'GET'     => [],
            'POST'    => [],
            'COOKIES' => [],
            'FILES'   => [],
            'SERVER'  => [],
            'PARAMS'  => [],
        ], $errorInfo->getParameters());
    }
}