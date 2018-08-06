<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Interop\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aidphp\Routing\Middleware\MethodMiddleware;

class NotAllowedMiddleware implements MiddlewareInterface
{
    public const TEMPLATE = 'not-allowed';

    protected $factory;
    protected $renderer;
    protected $template;

    public function __construct(ResponseFactoryInterface $factory, RendererInterface $renderer, string $template = self::TEMPLATE)
    {
        $this->factory  = $factory;
        $this->renderer = $renderer;
        $this->template = $template;
    }

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $methods = $req->getAttribute(MethodMiddleware::class);

        if (is_array($methods))
        {
            $res = $this->factory->createResponse(405)->withHeader('Allow', implode(',', $methods));

            $res->getBody()->write($this->renderer->render($this->template, ['method' => $req->getMethod()]));

            return $res;
        }

        return $handler->handle($req);
    }
}