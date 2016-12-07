<?php
/**
 * Desc: YunDunGuzzleHttpClient
 * Created by PhpStorm.
 * User: <gaolu@yundun.com>
 * Date: 2016/11/25 16:47
 */

namespace YunDunSdk\HttpClients;

use YunDunSdk\Exceptions\HttpClientException;
use YunDunSdk\Http\RawResponse;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use YunDunSdk\Http\HttpLib;
use GuzzleHttp\Middleware;
use YunDunSdk\Exceptions\ExceptionCodeMsg;

class YunDunGuzzleHttpClient implements YunDunHttpClientInterface{
    /**
     * @var \GuzzleHttp\Client The Guzzle client.
     */
    protected $guzzleClient;
    /**
     * @param \GuzzleHttp\Client|null The Guzzle client.
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }
    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut, $otherOptions = [])
    {
        if($body && !is_string($body)){
            throw new HttpClientException('guzzle body must be string');
        }
        $options = [
            'headers' => $headers,
            'timeout' => $timeOut,
            'connect_timeout' => 10,
        ];

        if(isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json'){
            $options['json'] = json_decode($body, true);
        }else if(isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/x-www-form-urlencoded'){
            parse_str($body, $content);
            $options['form_params'] = $content;
        }else{
            throw new HttpClientException(ExceptionCodeMsg::MSG_YUNDUNGUZZLEHTTPCLIENT_SEND_1, ExceptionCodeMsg::CODE_YUNDUNGUZZLEHTTPCLIENT_SEND_1);
        }

        if(isset($otherOptions['async']) && $otherOptions['async']){
            $callback = $otherOptions['callback']?:function(){};
            $exception = $otherOptions['exception']?:function(){};
            $promise = $this->guzzleClient->requestAsync($method, $url, $options);
            $promise->then(
                $callback,
                $exception
            )->wait();
            return 'asyncRequest';
        }

        try {
            $rawResponse = $this->guzzleClient->request($method, $url, $options);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();
            if (!$rawResponse instanceof ResponseInterface) {
                throw new HttpClientException($e->getMessage(), $e->getCode());
            }
        }
        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();
        return new RawResponse($rawHeaders, $rawBody, $httpStatusCode);

    }
    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param ResponseInterface $response The Guzzle response.
     *
     * @return string
     */
    public function getHeadersAsString(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name . ": " . implode(", ", $values);
        }
        return implode("\r\n", $rawHeaders);
    }
}