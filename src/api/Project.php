<?php

namespace Topkee\LangServicePhpsdk\api;

use phpDocumentor\Reflection\Types\This;

class Project extends BaseApi
{
    public static function checkServe(){
        $rs= self::httpRequst("https://market-api.topkee.top/v1/lang/checkserve");
        return $rs;
    }
}