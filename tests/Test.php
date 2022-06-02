<?php

namespace Topkee\LangServicePhpsdk\Test;
require_once __DIR__ . '/../vendor/autoload.php';
define("ROOT_PATH", dirname(__DIR__) . "/");

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use PHPUnit\Framework\TestCase;
use Topkee\LangServicePhpsdk\api\BaseApi;
use Topkee\LangServicePhpsdk\api\Instanze;
use Topkee\LangServicePhpsdk\api\Project;
use Topkee\LangServicePhpsdk\LangSdk;


class Test extends TestCase
{
    public function testTrim(){
        $ss=" sss ";
//        $this->Log()->info(trim($ss));
        $this->assertEquals(trim($ss),'sss');
    }
//    public function testSdk(){
//        $sdk=LangSdk::getInstance(1122,3344);
//        $that=$this;
//        $sdk2=$sdk->onLocaleMessage(function ($appid,$appsecret) use ($that) {
////            $that->Log()->info("参数 $appid,$appsecret");
//        });
//        $this->assertEquals($sdk,$sdk2);
//    }
//    public function testHttp(){
//        $client = new Client();
//        $res = $client->request('GET', 'https://market-api.topkee.top/v1/lang/checkserve', [
//
//        ]);
//        $rs=$res->getBody()->getContents();
//        $this->Log()->info("http $rs");
//        $this->assertNotEmpty($rs);
//    }
//    public function testCheckServe(){
//
//        $sdk=new LangSdk(1122,3344);
//        $rs=$sdk->checkServe();
//        $this->assertEquals($rs,true);
//    }
//
    public function testInit(){
        echo PHP_EOL;
        $sdk=LangSdk::getInstance('8b628d3f-0722-e015-1d65-45ddc7d4f158','6539c39ae177c281ee6ee522b1cf1efd');

//        $sdk->onLocaleMessage(function ($lang,$message) {
//            echo $lang.PHP_EOL.json_encode($message,JSON_PRETTY_PRINT).PHP_EOL;
//
//        });

//        $sdk->loadLocalesMessages([
//            "en"=>[
//                "test.test2"=>"test",
//                "care"=>[
//                    "add"=>"test add2"
//                ]
//
//             ]
//        ]);

        $messages=$sdk->getMessages();
        echo json_encode($messages,JSON_PRETTY_PRINT);
        $this->assertEquals(true,true);
    }
//    public function testArrayMerg(){
//
//       $arr=[
//           'a'=>'aa',
//           'b'=>'bb',
//           'c'=>'cc',
//           'd'=>[
//               'e'=>'ee'
//           ]
//       ];
//       $arr2=[
//           'a'=>'aa2',
//           'a.d.g'=>'gg2',
//           'bbb'=>'bbb2',
//           'c'=>'cc2',
//           'd'=>[
//               'e2'=>'ee2'
//           ]
//       ];
//       $arr3=array_merge(LangSdk::flatArray($arr),LangSdk::flatArray($arr2));
//       echo json_encode($arr3,JSON_PRETTY_PRINT);
//       $this->assertEquals(true,true);
//    }

    public function Log()
    {
        // create a log channel
        $log = new Logger('Tester');
        $log->pushHandler(new StreamHandler(ROOT_PATH . 'storage/logs/app.log', Logger::DEBUG));
//        $log->error("Error");
        return $log;
    }
}