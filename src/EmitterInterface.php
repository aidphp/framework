<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    function emit(ResponseInterface $res): bool;
}