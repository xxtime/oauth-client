<?php

namespace Xxtime\Oauth\Providers;


interface ProviderInterface
{


    public function verify($id = '', $token = '');


}