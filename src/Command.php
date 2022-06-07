<?php

namespace Topkee\LangServicePhpsdk;

class Command
{
    public static function main(){
        $args=$_SERVER['argv'];

        $command=new static;
        return $command->run($args);
    }
    public function run($args){
        $parms=self::parseArgs($args);
        if($parms){
            $path=rtrim($parms['PATH'],'/');
            echo "参数 ".json_encode($parms).PHP_EOL;
            if(!is_dir($path)){
                echo "目录$path 不存在 ".PHP_EOL;
                return 1;
            }
            $localmsg=LangSdk::loadLocalMessagesByPath($path);
            $sdk=LangSdk::getInstance($parms['APPID'],$parms['APPSECRET']);
//            echo "本地配置 ".json_encode($localmsg,JSON_PRETTY_PRINT).PHP_EOL;
            $project=$sdk->getProject();
            if(!$project){
                echo ("topkeelang-upload运行失败！ APPID或APPSECRET 错误");
                return 1;
            }
            if($localmsg&&count($localmsg)>0){
                $sdk->loadLocalesMessages($localmsg);
                $sdk->callSetLocaleMessage(true);
                echo ("本地配置已上传, 十秒后保存配置到本地").PHP_EOL;
                $index=0;
                while (++$index<10){
                   sleep(1);
                   echo ("$index s").PHP_EOL;
                }
            }else{
                echo ("未检测到本地配置").PHP_EOL;
            }
            $messages=$sdk->getMessages(true);
            foreach ($messages as $lang=>$message){
                self::replace($path."/$lang.json",$sdk->decodeUnicode(json_encode($message,JSON_PRETTY_PRINT)));
            }
            echo ("保存配置到本地").PHP_EOL;
            echo ("topkeelang-upload运行成功！").PHP_EOL;
            return 0;
        }else{
            return 1;
        }
    }
    public function replace($path, $content)
    {
        // If the path already exists and is a symlink, get the real path...
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $tempPath = tempnam(dirname($path), basename($path));

        // Fix permissions of tempPath because `tempnam()` creates it with permissions set to 0600...
        chmod($tempPath, 0777 - umask());

        file_put_contents($tempPath, $content);

        rename($tempPath, $path);
    }
    public function parseArgs($args)
    {
        $parms = [];
        foreach ($args as $arg){
            $subargs=explode('=',$arg);
            if(count($subargs)<2) continue;
            $parms[strtoupper($subargs[0])]=$subargs[1];
        }
       $parms_needs = ['APPID', 'APPSECRET', 'PATH'];
        foreach ($parms_needs as $parms_need){
            if (!isset($parms[$parms_need])){
                echo ("topkeelang-upload运行失败 缺少参数 $parms_need");
                $parms=null;
                break;
            }
        }
       return $parms;
    }

}