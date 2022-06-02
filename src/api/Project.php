<?php

namespace Topkee\LangServicePhpsdk\api;

use phpDocumentor\Reflection\Types\This;

class Project extends BaseApi
{
    public static function checkServe(){
        $rs= self::httpRequst("checkserve",'get',[],[],10);
        return $rs;
    }

    public static function getProject(string $appid, string $appsecret){
        $rs= self::httpRequst("projects/$appid",'get',['appsecret'=>$appsecret],[],10);
        return $rs;
    }

}