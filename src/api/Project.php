<?php

namespace Topkee\LangServicePhpsdk\api;

use phpDocumentor\Reflection\Types\This;

class Project extends BaseApi
{
    /** 获取项目最后改动时间+检测服务器是否正常
     * @param string $appid
     * @param string $appsecret
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function checkProject(string $appid, string $appsecret){
        $rs= self::httpRequst("checkserve",'get',['appid'=>$appid,'appsecret'=>$appsecret],[],10);
        return $rs;
    }

    /** 获取项目详情
     * @param string $appid
     * @param string $appsecret
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getProject(string $appid, string $appsecret){
        $rs= self::httpRequst("projects/$appid",'get',['appsecret'=>$appsecret],[],10);
        return $rs;
    }

}