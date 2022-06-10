<?php

namespace Topkee\LangServicePhpsdk\api;

class LangKvs extends BaseApi
{
    /**获取某个版本的某个语言配置
     * @param $appid
     * @param $appsecret
     * @param $version
     * @param $lang
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
      public static function exportKv($appid, $appsecret, $version, $lang){
          return self::httpRequst("export/$appid/$version/$lang"
              ,'get'
              ,[
                  'format_type'=>'json',
                  'appsecret'=>$appsecret,
              ]);
      }

    /** 上传某个语言配置
     * @param string $appid
     * @param string $appsecret
     * @param string $lang
     * @param $data
     * @param string $name
     * @param bool $check
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
      public static function importKv(string $appid,string  $appsecret,string $lang, $data,string  $name, bool $check=true){
          return self::httpRequst("import/$appid/$lang"
              ,'post'
              ,[
                  'data'=>is_string($data)? $data : json_encode($data),
                  'import_type'=> 'json',
                  'name'=>$name,
                  'appsecret'=>$appsecret,
                  'check'=>$check,
              ]);
      }
}