<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Aidphp\Framework\Emitter;

class EmitterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSend()
    {
        $content = 'Hello World!';

        $res = $this->createMock(ResponseInterface::class);
        $res->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $res->expects($this->once())
            ->method('getReasonPhrase')
            ->willReturn('OK');

        $res->expects($this->once())
            ->method('getProtocolVersion')
            ->willReturn('1.1');

        $res->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['Content-Type' => ['text/plain'], 'Content-Length' => [strlen($content)]]);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn($content);

        $res->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $emitter = new Emitter();
        ob_start();
        $this->assertSame(true, $emitter->emit($res));
        $body = ob_get_clean();
        $this->assertContains($content, $body);
    }
}