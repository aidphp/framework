<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Interop\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class NotFoundHandler implements RequestHandlerInterface
{
    public const TEMPLATE = 'not-found';

    protected $factory;
    protected $renderer;
    protected $template;

    public function __construct(ResponseFactoryInterface $factory, RendererInterface $renderer, string $template = self::TEMPLATE)
    {
        $this->factory  = $factory;
        $this->renderer = $renderer;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $req): ResponseInterface
    {
        $res = $this->factory->createResponse(404);

        $res->getBody()->write($this->renderer->render($this->template));

        return $res;
    }
}