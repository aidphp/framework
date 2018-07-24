<?php

declare(strict_types=1);

namespace Aidphp\Framework;

class ErrorInfoHelper
{
    public function getContent(ErrorInfo $error): string
    {
        return $this->getFileContent($error->getFile(), $error->getLine());
    }

    public function formatTrace(ErrorInfo $error): array
    {
        $traces = [];

        foreach ($error->getTrace() as $trace)
        {
            if (! isset($trace['file']))
            {
                continue;
            }

            if (! isset($trace['line']))
            {
                continue;
            }

            $traces[] = [
                'file' => $trace['file'],
                'line' => $trace['line'],
                'func' => (isset($trace['class']) ? $trace['class'] . '::' : '') . $trace['function'],
                'code' => $this->getFileContent($trace['file'], $trace['line']),
            ];
        }

        return $traces;
    }

    private function getFileContent(string $file, int $line): string
    {
        if (! is_file($file))
        {
            return '';
        }

        $content = file_get_contents($file);
        $lines   = explode("\n", $content);
        $start   = $line - 5;
        $length  = 10;
        if ($start < 0)
        {
            $start = 0;
        }

        $code = array_slice($lines, $start, $length, true);

        return implode("\n", $code);
    }
}