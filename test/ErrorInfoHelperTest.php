<?php

declare(strict_types=1);

namespace Test\Aidphp\Framework;

use PHPUnit\Framework\TestCase;
use Aidphp\Framework\ErrorInfo;
use Aidphp\Framework\ErrorInfoHelper;

class ErrorInfoHelperTest extends TestCase
{
    public function testGetContent()
    {
        $error = $this->createMock(ErrorInfo::class);
        $error->expects($this->once())
            ->method('getFile')
            ->willReturn(__FILE__);

        $error->expects($this->once())
            ->method('getLine')
            ->willReturn(0);

        $helper = new ErrorInfoHelper();
        $this->assertContains($helper->getContent($error), file_get_contents(__FILE__));
    }

    public function testGetEmptyContent()
    {
        $error = $this->createMock(ErrorInfo::class);

        $helper = new ErrorInfoHelper();
        $this->assertSame('', $helper->getContent($error));
    }

    public function testFormatTrace()
    {
        $error = $this->createMock(ErrorInfo::class);
        $error->expects($this->once())
            ->method('getTrace')
            ->willReturn([
                [
                    'file'     => __FILE__,
                    'line'     => 36,
                    'class'    => __CLASS__,
                    'function' => __METHOD__
                ],
            ]);

        $helper = new ErrorInfoHelper();
        $traces = $helper->formatTrace($error);

        $this->assertTrue(count($traces) > 0);
        $this->assertSame(__FILE__, $traces[0]['file']);
        $this->assertSame(36, $traces[0]['line']);
        $this->assertSame(__CLASS__ . '::' . __METHOD__, $traces[0]['func']);
        $this->assertContains($traces[0]['code'], file_get_contents(__FILE__));
    }

    public function testFormatEmptyTrace()
    {
        $error = $this->createMock(ErrorInfo::class);
        $error->expects($this->once())
            ->method('getTrace')
            ->willReturn([
                ['file' => __FILE__],
                ['line' => 30],
            ]);

        $helper = new ErrorInfoHelper();
        $this->assertSame([], $helper->formatTrace($error));
    }
}