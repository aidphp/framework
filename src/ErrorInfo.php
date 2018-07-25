<?php

declare(strict_types=1);

namespace Aidphp\Framework;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;

class ErrorInfo
{
    private $e;
    private $debug;
    private $req;

    public function __construct(Throwable $e, bool $debug = false, ServerRequestInterface $req = null)
    {
        $this->e     = $e;
        $this->debug = $debug;
        $this->req   = $req;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function getUri(): string
    {
        return $this->req ? $this->req->getUri()->__toString() : ($_SERVER['REQUEST_URI'] ?? '');
    }

    public function getMethod(): string
    {
        return $this->req ? $this->req->getMethod() : ($_SERVER['REQUEST_METHOD'] ?? '');
    }

    public function getType(): string
    {
        return get_class($this->e);
    }

    public function getCode()
    {
        return $this->e->getCode();
    }

    public function getFile(): string
    {
        return $this->e->getFile();
    }

    public function getLine(): int
    {
        return $this->e->getLine();
    }

    public function getMessage(): string
    {
        return $this->e->getMessage();
    }

    public function getTrace(): array
    {
        return $this->e->getTrace();
    }

    public function getParameters(): array
    {
        return $this->req ? [
            'GET'     => $this->req->getQueryParams(),
            'POST'    => $this->req->getParsedBody(),
            'COOKIES' => $this->req->getCookieParams(),
            'FILES'   => $this->req->getUploadedFiles(),
            'SERVER'  => $this->req->getServerParams(),
            'PARAMS'  => $this->req->getAttributes(),
        ] : [
            'GET'     => $_GET,
            'POST'    => $_POST,
            'COOKIES' => $_COOKIE,
            'FILES'   => $_FILES,
            'SERVER'  => $_SERVER,
            'PARAMS'  => [],
        ];
    }
}