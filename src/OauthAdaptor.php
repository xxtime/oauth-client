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


    /**
     * verify the token
     * @param string $id
     * @param string $token
     * @return mixed
     */
    public function verify($id = '', $token = '')
    {
        if (!method_exists($this->adaptor, "verify")) {
            throw new DefaultException("method is not found");
        }

        return $this->adaptor->verify($id, $token);
    }

    /**
     * this will redirect
     * @return mixed
     * @throws DefaultException
     */
    public function getCode()
    {
        if (!method_exists($this->adaptor, "getCode")) {
            throw new DefaultException("method is not found");
        }

        return $this->adaptor->getCode();
    }


    /**
     * get access token
     * @param string $code
     * @return mixed
     * @throws DefaultException
     */
    public function getAccessToken($code = '')
    {
        if (!method_exists($this->adaptor, "getAccessToken")) {
            throw new DefaultException("method is not found");
        }

        return $this->adaptor->getAccessToken($code);
    }

}
