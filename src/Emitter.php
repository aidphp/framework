<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Message\ResponseInterface;

class Emitter implements EmitterInterface
{
    public function emit(ResponseInterface $res): bool
    {
        $code = $res->getStatusCode();

        if (! headers_sent())
        {
            foreach ($res->getHeaders() as $name => $values)
            {
                foreach ($values as $value)
                {
                    header($name . ': ' . $value, false);
                }
            }

            $text = $res->getReasonPhrase();

            header('HTTP/' . $res->getProtocolVersion() . ' ' . $code . ($text ? ' ' . $text : ''), true, $code);
        }

        if ($code > 199 && !in_array($code, [204, 205, 304], true))
        {
            echo $res->getBody()->__toString();
        }

        return true;
    }
}