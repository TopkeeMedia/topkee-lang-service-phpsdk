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
    public function testSdk(){
        $sdk=new LangSdk(1122,3344);
        $that=$this;
        $sdk2=$sdk->setOnLocaleMessage(function ($appid,$appsecret) use ($that) {
//            $that->Log()->info("参数 $appid,$appsecret");
        });
        $this->assertEquals($sdk,$sdk2);
    }
//    public function testHttp(){
//        $client = new Client();
//        $res = $client->request('GET', 'https://market-api.topkee.top/v1/lang/checkserve', [
//
//        ]);
//        $rs=$res->getBody()->getContents();
//        $this->Log()->info("http $rs");
//        $this->assertNotEmpty($rs);
//    }
    public function testCheckServe(){

        $rs=Project::checkServe();
        $this->Log()->info("testCheckServe ".json_encode($rs));
        $this->assertNotEmpty($rs);
    }

    public function Log()
    {
        // create a log channel
        $log = new Logger('Tester');
        $log->pushHandler(new StreamHandler(ROOT_PATH . 'storage/logs/app.log', Logger::DEBUG));
//        $log->error("Error");
        return $log;
    }
}