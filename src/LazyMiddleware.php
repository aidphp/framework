<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aidphp\Pipeline\HandlerMiddleware;

class LazyMiddleware implements MiddlewareInterface
{
    protected $dic;
    protected $name;

    public function __construct(ContainerInterface $dic, string $name)
    {
        $this->dic  = $dic;
        $this->name = $name;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->dic->get($this->name);

        if ($middleware instanceof RequestHandlerInterface)
        {
            $middleware = new HandlerMiddleware($middleware);
        }

        return $middleware->process($req, $handler);
    }
}