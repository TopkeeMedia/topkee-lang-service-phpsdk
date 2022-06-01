<?php

namespace Topkee\LangServicePhpsdk\api;

class Version extends BaseApi
{

    public static function getVersion($appid, $appsecret, $version){
        return self::httpRequst("projects/$appid/versions/$version",'get',['appsecret'=>$appsecret]);
    }
}