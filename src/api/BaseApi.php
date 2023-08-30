<?php

namespace Topkee\LangServicePhpsdk\api;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class BaseApi
{
    const url="https://lang.cs.topkee.com/v1/lang/";
//    const url="http://localhost:8089/v1/lang/";
    public static function getUrl(){
      return self::url;
    }
    /** http请求方法
     * @param string $api
     * @param string $type
     * @param array $faxData
     * @param array $headers
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function httpRequst(string $api, string $type = 'GET', array $faxData = [], array $headers = [],int $timeout=60):array
    {
        $url=self::getUrl().$api;
//        $fun_args=json_encode($faxData);
        $client = new Client(['timeout' => $timeout]);
        if (in_array(strtoupper($type), ['GET', 'DELETE'])) {
            $options['query'] = $faxData;
        } else {
            $options['json'] = $faxData;
        }
        $headers['Content-type'] = 'application/json;charset=utf-8';
        $options['headers']      = $headers;
        $options['verify']       = false;
        try {
            $response       = $client->request($type, $url, $options);
            $body           = $response->getBody();
            $remainingBytes = $body->getContents();
            $res            = json_decode($remainingBytes, true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            /** @var ResponseInterface $response */
            $response = $e->getResponse();
            $res      = json_decode($response->getBody()->getContents(), true);
            $msg      = isset($res['message']) ? $e->getMessage() . $res['message'] : $e->getMessage();
            throw new \Exception("lang service接口异常 $msg");
        }

        return $res;
    }

}