<?php

namespace Klev\CryptoPayApi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface CryptoPayHttpClientInterface
{
    /**
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     * @return ResponseInterface
     */
    public function get($uri, array $options = []): ResponseInterface;

    /**
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     * @return ResponseInterface
     */
    public function post($uri, array $options = []): ResponseInterface;
}