<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Aidphp\Error\ErrorHandlerInterface;
use Psr\Container\ContainerInterface;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class LazyErrorHandler implements ErrorHandlerInterface
{
    protected $dic;

    public function __construct(ContainerInterface $dic)
    {
        $this->dic = $dic;
    }

    public function handleError(Throwable $e, ServerRequestInterface $req = null): ResponseInterface
    {
        return $this->dic->get(ErrorHandlerInterface::class)->handleError($e, $req);
    }
}