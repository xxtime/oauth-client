<?php

/**
 * OAuth 2.0定义了四种授权方式
 * 1. 授权码模式（authorization code）
 * 2. 简化模式（implicit）
 * 3. 密码模式（resource owner password credentials）
 * 4. 客户端模式（client credentials）
 *
 * @link http://www.ruanyifeng.com/blog/2014/05/oauth_2_0.html
 */

namespace Xxtime\Oauth;


class OauthAdaptor
{

    private $adaptor;


    // $option = ['clientId'=>'', 'clientSecret'=>''];
    public function __construct($adaptor = '', $option = [])
    {
        if (!$adaptor) {
            throw new DefaultException('no adaptor');
        }
        $class         = "\\Xxtime\\Oauth\\Providers\\" . ucfirst($adaptor);
        $this->adaptor = new $class($option);
    }


    public function verify($id = '', $token = '')
    {
        return $this->adaptor->verify($id, $token);
    }

    /**
     * 1. getCode
     * 2. getAccessToken
     * @return mixed
     */
    public function oauth()
    {
        return $this->adaptor->oauth();
    }

}
