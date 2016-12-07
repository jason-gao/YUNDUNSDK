# YUNDUN API PHP SDK legend

+	接口基地址： 'http://api.yundun.cn/V1/';
+	接口遵循RESTful,默认请求体json,接口默认返回json
+	app_id, app_secret 联系技术客服，先注册一个云盾的账号，用于申请绑定api身份

+   环境要求：php >=5.5
         
##使用步骤
1.	composer require yundun/yundunsdk dev-master
 
2.	实例化
```
    //sdk
    require 'xx/vendor/autoload.php';
    $app_id = 'xx';
    $app_secret = 'xx';
    $client_ip = 'xx';
    $client_userAgent = '';
    $base_api_url = 'http://api.yundun.cn/V1/';
    $handler = 'guzzle'; //curl/guzzle默认guzzle,guzzle支持异步调用，curl驱动目前未实现异步
    $sdk = new YundunSDK ([
        'app_id'=>$app_id, 
        'app_secret'=>$app_secret, 
        'client_ip'=>$client_ip, 
        'client_userAgent'=>$client_userAgent, 
        'handler'=> $handler]);

```

3. 调用

>   format json返回json，xml返回xml

>   body 支持传递json和数组

>   urlParams会拼接在url后面

>   支持get/post/patch/put/delete方法


+ get

```

$request = array(
    'url' => 'api/version',
    'body' => '',
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
);
try{
    $res = $sdk->get($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
exit($res);

```

+ post/put/patch/delete

```

$request = array(
    'url' => 'api/version',
    'body' => json_encode([
        'body1' => 1,
        'body2' => 2,
    ]),
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
);
try{
    $res = $sdk->post($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}
exit($res);

```


## async request

+ get

```

$request = array(
    'url' => 'api/version',
    'body' => '',
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
    'options' => [
        'async' => true,
        'callback' => function($response){
            $body = $response->getBody->getContents();
            echo $body;
            exit;
        },
        'exception' => function($exception){}
    ]
);
try{
    $sdk->getAsync($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}


```

+ post/put/patch/delete

```

$request = array(
    'url' => 'api/version',
    'body' => json_encode([
        'body1' => 1,
        'body2' => 2,
    ]),
    'headers' => [
        'format' => 'json',
    ],
    'timeout' => 10,
    'query' => [
        'params1' => 1,
        'params2' => 2
    ],
    'options' => [
        'async' => true,
        'callback' => function($response){
            $body = $response->getBody->getContents();
            echo $body;
            exit;
        },
        'exception' => function($exception){}
    ]
);
try{
    $sdk->postAsync($request);
}catch (\Exception $e){
    var_dump($e->getCode());
    var_dump($e->getMessage());
}

```