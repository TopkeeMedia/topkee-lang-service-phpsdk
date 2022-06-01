<?php

namespace Topkee\LangServicePhpsdk\api;

class LangKvs extends BaseApi
{
      public static function exportKv($appid, $appsecret, $version, $lang){
          return self::httpRequst("export/$appid/$version/$lang"
              ,'get'
              ,[
                  'format_type'=>'json',
                  'appsecret'=>$appsecret,
              ]);
      }

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