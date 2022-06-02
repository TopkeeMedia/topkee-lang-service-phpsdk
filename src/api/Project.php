<?php

namespace Topkee\LangServicePhpsdk\api;

use phpDocumentor\Reflection\Types\This;

class Project extends BaseApi
{
    public static function checkProject(string $appid, string $appsecret){
        $rs= self::httpRequst("checkserve",'get',['appid'=>$appid,'appsecret'=>$appsecret],[],10);
        return $rs;
    }

    public static function getProject(string $appid, string $appsecret){
        $rs= self::httpRequst("projects/$appid",'get',['appsecret'=>$appsecret],[],10);
        return $rs;
    }

}