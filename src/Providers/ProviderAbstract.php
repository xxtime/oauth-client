<?php

namespace Xxtime\Oauth\Providers;


use Xxtime\CurlUtils\CurlUtils;

abstract class ProviderAbstract implements ProviderInterface
{

    protected $http;


    protected $option;


    public function __construct($option = [])
    {
        $this->option = $option;

        $this->http = new CurlUtils();
        $this->http->setOptions([CURLOPT_HEADER => false]);
    }

}