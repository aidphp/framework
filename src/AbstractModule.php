<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Aidphp\Di\CompositeContainerInterface;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractModule implements MiddlewareInterface
{
    protected $dic;

    public function __construct(CompositeContainerInterface $dic)
    {
        $this->dic = $dic;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->dic->push($this->createContainer());

        $res = $this->createPipeline($this->dic)->process($req, $handler);

        $this->dic->pop();

        return $res;
    }

    abstract protected function createContainer(): ContainerInterface;

    abstract protected function createPipeline(ContainerInterface $dic): MiddlewareInterface;
}