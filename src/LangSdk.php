<?php

namespace Topkee\LangServicePhpsdk;

use NestedJsonFlattener\Flattener\Flattener;
use Topkee\LangServicePhpsdk\api\LangKvs;
use Topkee\LangServicePhpsdk\api\Project;
use Topkee\LangServicePhpsdk\api\Version;

class LangSdk
{
    private  $langNames=[
          'zh'=> '简体中文',
          'zh-CN'=> '简体中文',
          'zh_CN'=>'简体中文',
          'zh_HK'=> '繁體中文',
          'zh-HK'=> '繁體中文',
          'zh_TW'=> '繁體中文',
          'zh-TW'=> '繁體中文',

          'en-US'=>'English U.S.',
          'en'=> 'English',

          'ja'=> '日本語',
          'ja-JP'=> '日本語',
          'ja_JP'=>'日本語',

          'ko-KR'=> '한국어',
          'ko_KR'=> '한국어',
          'ko'=> '한국어',
        ];
    private $appid;
    private $appsecret;
    private $version='latest';
    private $onLocaleMessageClosure;
    /**
     * @var array
     * 形如
     * "zh_CN": {
     *   "modeule.key1": "xxx",
     *   "modeule2.key2": "xxx",
     *   "modeule.sub_modeule.key3": "xxx",
     *  },
     * "en":{
     *   "modeule.key1": "xxx",
     *   "modeule2.key2": "xxx",
     *   "modeule.sub_modeule.key3": "xxx",
     *  }
     */
    public $messages;
    /**
     * @var bool
     */
    private $serveLive;
    /**
     * @var mixed|void
     */
    private $project;
    /**
     * @var array
     */
    private $messages_serve;
    /**
     * @var mixed
     */
    private $versionObj;


    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance = NULL;

    /**
     * 静态工厂方法，返还此类的唯一实例
     * @throws \Exception
     */
    public static function getInstance(string $appid, string $appsecret) {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($appid, $appsecret);
            // 或者这样写
            // self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 防止用户克隆实例
     */
    public function __clone(){
        die('Clone is not allowed.' . E_USER_ERROR);
    }

    private function __construct(string $appid, string $appsecret)
    {
        if(empty($appid)||empty($appid)){
            throw new \Exception("缺少 appsecret或者appid");
        }
        $this->appsecret =trim($appsecret);
        $this->appid = trim($appid);
        $this->serveLive = $this->checkServe();
        $this->project = $this->getProject();
        $this->messages = $this->getMessages();

    }
    public function onLocaleMessage(\Closure $onLocaleMessage){
        $this->onLocaleMessageClosure=$onLocaleMessage;
        return $this;
    }

    public function loadLocalesMessages(array $messages): array
    {
        $messagesObj = is_string($messages)?json_decode($messages,true):$messages;
        foreach ($messagesObj as $key=>&$value){
            if(strpos($key,'.') !== false){
                throw new \Exception("loadLocalesMessages参数异常，应为{\"en\":{\"key1.key2.key3\":\"xxx\"},\"lang2\":{\"key1.key2.key3\":\"xxx\"}} 这种结构");
            }
            if(empty($value)){
                $value=[];
                continue;
            }
            if(!is_array($value)){
                throw new \Exception("语言 $key 配置必须是数组形式");
            }
            $value=self::flatArray($value);
        }
        // 以服务器配置优先
        $this->messages=self::mergMessages($messagesObj,$this->messages);
        $this->callSetLocaleMessage();
        return $this->messages;
    }
    public function callSetLocaleMessage(bool $withUpdate=false){
        if ($withUpdate) {
            $this->addLang2Serve();
        }
        $messages=$this->getMessages();
        if($this->onLocaleMessageClosure&&$messages){
            foreach ($messages as $key=>$message){
                $this->onLocaleMessageClosure->call($this,$key,$message);
            }
        }
    }
    private function addLang2Serve(){
        $messages=$this->getMessages();
        $langsCount=count($messages);
        $index=0;
        foreach ($messages as $key=>$message){
            $index++;
            $check=$langsCount===$index;
            self::import2Serve($key, $message, $check);
        }
    }
    private function import2Serve(string $code, $messages,bool $check=true){
        $name =isset($this->langNames[$code])?$this->langNames[$code]:$code;
        LangKvs::importKv($this->appid,$this->appsecret,$code,$messages,$name,$check);
    }
    private function getLangKv($lang){
        return json_decode(LangKvs::exportKv($this->appid,$this->appsecret,$this->version,$lang['code'])['data']['conf'],true);
    }
    public function getProject()
    {
        if($this->project){
            return $this->project;
        }
        return Project::getProject($this->appid,$this->appsecret)['data'];
    }
    public function serverLiving(): bool
    {
        if($this->serveLive){
            return $this->serveLive;
        }
        return self::checkServe();
    }
    public function getMessages(): array
    {
        if($this->messages_serve&&$this->messages){
            return $this->messages;
        }
        try {
            $messages_serve = $this->getServeMessages();
            if($messages_serve) {
               $this->messages =self::mergMessages($this->messages,$messages_serve);
               $this->messages_serve = $messages_serve;
            }
        } catch (\Exception $exception) {}

        return $this->messages;
    }
    public function getServeMessages(): ?array
    {
        if ($this->project ||$this->serveLive) {
            try {
                try {
                    $this->versionObj =Version::getVersion($this->appid, $this->appsecret, $this->version)['data'];
                } catch (\Exception $exception) {
                    if ($this->version !== 'latest') {
                        $this->version = 'latest';
                        return $this->getServeMessages();
                    }
                    return null;
                }
                if ( count($this->versionObj['langs']) === 0) {
                    return null;
                }
                $localesMessages=[];
                foreach ($this->versionObj['langs'] as $lang){
                    $localesMessages[$lang['code']]=self::flatArray($this->getLangKv($lang));
                }
                return $localesMessages;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return [];
    }
    public function getVersion(){}
    public function checkServe():bool{
        try {
            Project::checkServe();
            return true;
        }catch (\Exception $exception){
            return false;
        }
    }

    public function mergMessages(?array $old_messages,?array $new_messages): array
    {
        $old_messages=$old_messages??[];
        $new_messages=$new_messages??[];
        foreach ($new_messages as $key2=>&$message2){
            $message2=self::flatArray($message2);
        }
        foreach ($old_messages as $key=>&$message){
            $message=self::flatArray($message);
            if(isset($new_messages[$key])){//包含该语言，合并
                $new_message=$new_messages[$key];
                $new_messages[$key]=array_merge($message,$new_message);
            }else{
                $new_messages[$key]=$message;// 包含该语言，添加
            }
        }



        return $new_messages;
    }
    /**
     * @param $messagesObj
     */
    public static function flatArray(?array $messagesObj): array
    {
        $flattener = new Flattener();
        $flattener->setArrayData($messagesObj??[]);
        $arr=$flattener->getFlatData();
        return is_array($arr[0])?$arr[0]:$arr;
    }

}