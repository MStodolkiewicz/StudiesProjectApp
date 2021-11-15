<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\FirewallMapInterface;

class CustomApiHandlingFirewallMap implements FirewallMapInterface
{

    /**
     * @var FirewallMap
     */
    private $decorated;

    public function __construct($decorated)
    {
        $this->decorated = $decorated;
    }

    public function getListeners(Request $request)
    {
        $this->decorated->getListeners();
        dd('jestem!');
    }


}