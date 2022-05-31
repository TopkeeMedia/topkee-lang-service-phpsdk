<?php

namespace Topkee\LangServicePhpsdk;

class LangSdk
{
    private $appid;
    private $appsecret;
    private $version='latest';
    private $onLocaleMessage;

    public function __construct(string $appid, string $appsecret)
    {
        if(empty($appid)||empty($appid)){
            throw new \Exception("缺少 appsecret或者appid");
        }
        $this->appsecret =trim($appsecret);
        $this->appid = trim($appid);

    }
    public function setOnLocaleMessage(\Closure $onLocaleMessage){
        $this->onLocaleMessage=$onLocaleMessage;
        $this->onLocaleMessage->call($this,$this->appid,$this->appsecret);
        return $this;
    }
    public function init(){}
    public function loadLocalesMessages($messages){}
    public function callSetLocaleMessage($withUpdate){}
    private function addLang2Serve(){}
    private function import2Serve(){}
    private function getLangKv(){}
    private function getProject(){}
    private function checkDeleteLangs(){}
    public function serverLiving(){}
    public function getMessages(){}
    public function getServeMessages(){}
    public function getVersion(){}
    public function checkServe():bool{
        return false;
    }

}