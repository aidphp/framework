<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Aidphp\Error\ErrorHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Interop\Renderer\RendererInterface;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorHandler implements ErrorHandlerInterface
{
    public const TEMPLATE = 'server-error';

    protected $factory;
    protected $renderer;
    protected $debug;
    protected $template;

    public function __construct(ResponseFactoryInterface $factory, RendererInterface $renderer, bool $debug = false, string $template = self::TEMPLATE)
    {
        $this->factory  = $factory;
        $this->renderer = $renderer;
        $this->debug    = $debug;
        $this->template = $template;
    }

    public function handleError(Throwable $e, ServerRequestInterface $req = null): ResponseInterface
    {
        $res = $this->factory->createResponse(500);

        $res->getBody()->write($this->renderer->render($this->template, ['error' => new ErrorInfo($e, $this->debug, $req)]));

        return $res;
    }
}