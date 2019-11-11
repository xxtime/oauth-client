## Oauth-client
Oauth-client is use for the third-part account login verify on the backend server.  
It support Google, Facebook, Weixin, Weibo etc.


## Install
```bash
composer require xxtime/oauth-client
```


## How to use it

```php
$id = '{Google account id}';
$token = '{Google login account token}';
$option = [
    'clientId' => '{Google app id}'
];
try {
    $oauth = new OauthAdaptor('google', $option);
    $user = $oauth->verify($id, $token);
    print_r($user);
} catch (\Exception $e) {
    echo $e->getMessage();
}
```
