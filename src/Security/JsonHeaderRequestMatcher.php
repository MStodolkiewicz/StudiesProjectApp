<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonHeaderRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        if($request->headers->get('Content-type') != "application/json"){
//            dd('siurak');
            return false;
        }
    }
}
