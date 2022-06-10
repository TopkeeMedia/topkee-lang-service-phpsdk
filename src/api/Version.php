<?php

namespace Topkee\LangServicePhpsdk\api;

class Version extends BaseApi
{
    /** 获取版本详情
     * @param $appid
     * @param $appsecret
     * @param $version
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getVersion($appid, $appsecret, $version){
        return self::httpRequst("projects/$appid/versions/$version",'get',['appsecret'=>$appsecret]);
    }
}