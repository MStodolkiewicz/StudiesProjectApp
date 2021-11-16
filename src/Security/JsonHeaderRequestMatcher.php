<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonHeaderRequestMatcher extends RequestMatcher
{
    public function __construct(string $path = null, string $host = null, $methods = null, $ips = null, array $attributes = [], $schemes = null, int $port = null)
    {
        parent::__construct($path, $host, $methods, $ips, $attributes, $schemes, $port);
    }

    public function matches(Request $request)
    {
        return (parent::matches($request) && $this->isApiCall($request));
    }

    public function isApiCall(Request $request){
        return str_contains($request->getPathInfo(),'/api')  && $request->headers->get('Content-type') == "application/json";
    }
}
