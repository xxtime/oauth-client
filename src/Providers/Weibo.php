<?php

/**
 * 新浪微博OAuth
 * @link https://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6
 * @link https://open.weibo.com/wiki/Oauth2/authorize
 * @link https://open.weibo.com/wiki/Oauth2/access_token
 * @link https://open.weibo.com/wiki/Oauth2/get_token_info
 * @link https://open.weibo.com/wiki/Oauth2/revokeoauth2
 */

namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Weibo extends ProviderAbstract
{


    private $endpoint = 'https://api.weibo.com';


    public function __construct(array $option)
    {
        parent::__construct($option);

        if (empty($this->option['clientId']) || empty($this->option['clientSecret'])) {
            throw new DefaultException('missing clientId or clientSecret');
        }
    }


    public function oauth()
    {
        $code = isset($_GET["code"]) ? $_GET["code"] : null;
        if (!$code) {
            $this->getCode();
            exit();
        }

        $response = $this->getAccessToken($code);
        $result   = [
            "accessToken" => $response["access_token"],
            "expires"     => $response["expires_in"],
            "uid"         => $response["uid"],
        ];
        return $result;
    }


    /**
     * @see https://open.weibo.com/wiki/Oauth2/authorize
     */
    private function getCode()
    {
        $uri = "/oauth2/authorize";

        $params = [
            "client_id"    => $this->option["clientId"],
            "redirect_uri" => $this->option["redirect"],
            "display"      => "mobile",
            "forcelogin"   => false,
            "language"     => "zh",
        ];
        if (isset($this->option["scope"])) {
            $params["scope"] = $this->option["scope"];
        }
        if (isset($this->option["state"])) {
            $params["state"] = $this->option["state"];
        }
        if (isset($this->option["display"])) {
            $params["display"] = $this->option["display"];
        }

        $url = $this->endpoint . $uri . "?" . http_build_query($params);
        header("Location:" . $url);
        return;
    }


    /**
     * @link https://open.weibo.com/wiki/Oauth2/access_token
     * @param string $code
     * @return mixed
     */
    private function getAccessToken($code = "")
    {
        $uri    = "/oauth2/access_token";
        $params = [
            "client_id"     => $this->option["clientId"],
            "client_secret" => $this->option["clientSecret"],
            "grant_type"    => "authorization_code",
            "code"          => $code,
            "redirect_uri"  => $this->option["redirect"],
        ];

        $response = $this->http->post($this->endpoint . $uri, $params);
        $result   = json_decode($response, true);
        return $result;
    }

    public function verify($id = '', $token = '')
    {
        // TODO: Implement verify() method.
    }


}
