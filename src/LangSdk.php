<?php

namespace Topkee\LangServicePhpsdk;

use NestedJsonFlattener\Flattener\Flattener;
use phpDocumentor\Reflection\Types\This;
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
    private $appid=null;
    private $appsecret=null;
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
    public $messages=[];
    /**
     * @var bool
     */
    private $serveLive=false;
//    /**
//     * @var mixed|void
//     */
//    private $project=null;
    /**
     * @var array
     */
    private $messages_serve=[];
    /**
     * @var mixed
     */
    private $versionObj=null;


    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance = NULL;
    public static $updated_at=0;
    public $latestCheckTime=0;
    /**
     * @var bool
     */
    public $needGetServeMessages=true;
    private $project=null;

    /**
     * 静态工厂方法，返还此类的唯一实例
     * @throws \Exception
     */
    public static function getInstance(string $appid, string $appsecret) {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($appid, $appsecret);
        }

        return self::$_instance;
    }

    /**
     * @param $path
     * @param $file
     * @return array|string|string[]
     */
    private static function getRPath($path, $file,$ext)
    {
        $rPath = str_replace($path, '', $file);
        $rPath = str_replace($ext, '', $rPath);
        $rPath = str_replace('/', '.', trim($rPath, '/'));
        return $rPath;
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
        $this->serveLive = $this->serverLiving();
    }

    public function onLocaleMessage(\Closure $onLocaleMessage){
        $this->onLocaleMessageClosure=$onLocaleMessage;
        return $this;
    }

    /**
     *
     * @param array $messages 0到多个语言配置 {en:{...},zh:{...}}
     * @return array
     */
    public function loadLocalesMessages(array $messages): array
    {


        $messagesObj = is_string($messages)?json_decode($messages,true):$messages;
        foreach ($messagesObj as $key=>&$value){
            if(is_array($value)) $value=self::flatArray($value);
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
        if(!$this->serveLive) return;
        $name =isset($this->langNames[$code])?$this->langNames[$code]:$code;
        LangKvs::importKv($this->appid,$this->appsecret,$code,$messages,$name,$check);
    }
    private function getLangKv($lang){
        if(!$this->serveLive) return [];
        $conf=LangKvs::exportKv($this->appid,$this->appsecret,$this->version,$lang['code'])['data']['conf'];
        return json_decode($conf,true);
    }
    public function getProject()
    {
        if(!$this->serveLive) return null;
        if($this->project){
            return $this->project;
        }
        $this->project=Project::getProject($this->appid,$this->appsecret)['data'];
        return $this->project;
    }
    public function serverLiving(): bool
    {
        return self::checkProject()!==false;
    }
    public function getMessages($loadImmediately=false): array
    {
        try {
            $messages_serve = $this->getServeMessages($loadImmediately);
            if($messages_serve&&$messages_serve!==$this->messages_serve) {
               $this->messages =self::mergMessages($this->messages,$messages_serve);
               $this->messages_serve = $messages_serve;
            }
        } catch (\Exception $exception) {}
        return $this->messages;
    }
    public function getServeMessages($loadImmediately=false): ?array
    {
        if ($loadImmediately||($this->serveLive&&self::checkIfneedGetServeMessages())) {
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
                $this->needGetServeMessages= false;
                // 记住改项目的服务器更新最后时间，这样就不会频繁请求服务器
                $updated_at=self::checkProject();
                if($updated_at!==false){
                    self::$updated_at=$updated_at;
                }
                return $localesMessages;
            } catch (\Exception $exception) {
                throw $exception;
            }

        }
        return $this->messages_serve;
    }
    public function getVersion(){
        return $this->versionObj;
    }
    public function checkIfneedGetServeMessages():bool
    {
        $now=strtotime('now');
        if( $this->latestCheckTime+30>$now) return $this->needGetServeMessages;
        $updated_at=self::checkProject();
        $this->latestCheckTime=$now;
        if($updated_at&&$updated_at>self::$updated_at){
            $this->needGetServeMessages= true;
        }else{
            $this->needGetServeMessages= false;
        }
        return $this->needGetServeMessages;
    }

    public function checkProject(){
        try {
            $updated_at=Project::checkProject($this->appid,$this->appsecret)['data']['updated_at']??100;
            return $updated_at;
        }catch (\Exception $exception){
            return false;
        }
    }

    public static function mergMessages(?array $old_messages,?array $new_messages): array
    {
//        $start_time = microtime(true);                         //获取程序开始执行的时间
        $old_messages=$old_messages??[];
        $new_messages=$new_messages??[];
        foreach ($new_messages as $key2=>&$message2){
            if(is_array($message2)) {
                $message2=self::flatArray($message2);
            }
        }
        foreach ($old_messages as $key=>&$message){
            if(is_array($message)){
                $message=self::flatArray($message);
                if(isset($new_messages[$key])){//包含该语言，合并
                    $new_message=$new_messages[$key];
                    $new_messages[$key]=array_merge($message,$new_message);
                }else{
                    $new_messages[$key]=$message;// 包含该语言，添加
                }
            }else{
                if(!isset($new_messages[$key])){
                    $new_messages[$key]=$message;
                }
            }
        }

//        $end_time = microtime(true);                        //获取程序执行结束的时间
//        $run_time = ($end_time - $start_time) * 1000;       //计算差值 毫秒
//        echo "mergMessages：$run_time 毫秒";

        return $new_messages;
    }
    /**
     * 嵌套数组转扁平数组
     * @param $deepArr
     */
    public static function flatArray(?array $deepArr): array
    {
        $flattener = new Flattener();
        $flattener->setArrayData($deepArr??[]);
        $arr=$flattener->getFlatData();
        return is_array($arr[0])?$arr[0]:$arr;
    }
    private static function  deepPandding(array $arr,array $subkeyArr,$pandding)
    {
        if(count($subkeyArr)==1){
            $key= $subkeyArr[0];
            $arr[$key]=$pandding;
            return $arr;
        }
        $key= array_pop($subkeyArr);
        if(!isset($arr[$key])){
            $arr[$key]=[];
        }
        $subArr=$arr[$key];
        $arr[$key]=self::deepPandding($subArr,$subkeyArr,$pandding);
        return $arr;

    }

    /** 扁平数组转为嵌套数组
     * @param array $flatArr
     * @return array
     */
    public static function flatArr2deep(array $flatArr):array
    {
        $rs=[];
        $flatArr=self::flatArray($flatArr);
        $langArr=[];
        foreach ($flatArr as $key=>$value){
            if(!self::checkKey($key)){ // 包含特殊字符，比如汉字，_.:-以外的标点符号，则不进行嵌套处理
                $lang = explode('.', $key)[0];
                $realKey=substr($key,(strlen($lang)+1));
                if(!isset($langArr[$lang])) $langArr[$lang]=[];
                $langArr[$lang][$realKey]=$value;
            }else{
                $subkeyArr = array_reverse(explode('.', $key));
                $rs=self::deepPandding($rs,$subkeyArr,$value);
            }
        }
        $rs= self::mergMessages($langArr,$rs);
        return $rs;
    }
    private static function checkKey($key){
        return  preg_match("/^[a-zA-Z0-9_.:-]{1,}$/", $key);
    }
    public static function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($matches){return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");}, $str);
    }

    private static function get_allfiles($path,&$files) {
        if(is_dir($path)){
            $dp = dir($path);
            while ($file = $dp ->read()){
                if($file !="." && $file !=".."){
                    self::get_allfiles($path."/".$file, $files);
                }
            }
            $dp ->close();
        }
        if(is_file($path)){
            $files[] =  $path;
        }
    }
    /**
     * 判断字符串是否以指定字符串结尾
     * @param string $str 原字串
     * @param string $needle 结尾字串
     * @return bool
     */
    public static function endWith($str, $needle) {
        $length = strlen($needle);
        if($length == 0)
        {
            return true;
        }
        return (substr($str, -$length) === $needle);
    }
    private static function getRequire($path, array $data = [])
    {
        if (is_file($path)) {
            $__path = $path;
            $__data = $data;
            return (static function () use ($__path, $__data) {
                extract($__data, EXTR_SKIP);
                return require $__path;
            })();
        }

        throw new \Exception("File does not exist at path {$path}.");
    }
    /**
     * 从给定的文件中加载语言包
     */
    public static function loadLocalMessagesByPath($path)
    {
        $files=[];
        self::get_allfiles($path,$files);
        $output=[];
        foreach ($files as $file) {
            if (file_exists($file)) {
                if(self::endWith($file,'.php')){
                    $rPath = self::getRPath($path, $file,'.php');
                    $arr=self::getRequire($file);

                }else if(self::endWith($file,'.json')){
                    $arr =json_decode(file_get_contents($file),true);
                    if (is_null($arr) || json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception("Translation file [{$file}] contains an invalid JSON structure.");
                    }
                    $rPath = self::getRPath($path, $file,'.json');
                }else{
                    return [];
                }
                $arr=self::flatArray($arr);
                if($rPath){
                    $new_arr=[];
                    foreach ($arr as $key=>$val){
                        $new_arr["$rPath.$key"]=$val;
                    }
                    $arr=$new_arr;
                }
                $output = array_merge($output, $arr);
            }
        }
        return self::flatArr2deep($output);
    }

}