
## 在多语言管理后添加项目
 记住项目的appid和appsecret， 假设你的项目  appid='111111'  appsecret='222222'
 
## 安装php版sdk
```cmd
composer require topkee/topkee-lang-service-phpsdk
```
## 使用
```php
        // 假设这是本地配置
        $localMessages=[
            "en"=>[
                "test.test2"=>"test",
                "care"=>[
                    "add"=>"test add2"
                ]

             ]
        ];
        $appid='111111';
        $appsecret='222222';
       // 1创建sdk实例
        $sdk=new LangSdk($appid,$appsecret);
//        $sdk->onLocaleMessage(function ($lang,$message) {
//            echo "语言 $lang 配置： ".json_encode($message,JSON_PRETTY_PRINT).PHP_EOL;
//
//        });
        // 2 初始化
        $sdk->init();
        // 加载本地配置到sdk
        $sdk->loadLocalesMessages($localMessages);
        // 4 获取合并后的多语言配置
        $messages=$sdk->getMessages();
//        echo json_encode($messages);
//        {
//            "zh_CN": {
//                "care.add": "添 加",
//                "care.addCare": "添加客服",
//                "care.addGroup": "新建分组"
//
//            },
//            "en": {
//                "test.test2"=>"test",
//                "care.add": "Add",
//                "care.addCare": "Add Care",
//                "care.addGroup": "New Group"
//            },
//            "zh_TW": {
//                "care.add": "添加",
//                "care.addCare": "添加客服",
//                "care.addGroup": "新建分組"
//            }
//        }

        // 5 安装i18n做翻译
        // composer require alpakaio/i18next-php
        $i18n = new I18n([
            'lng'           =>  'en',
            'resources'     =>  $messages
        ]);

        $i18n->t('care.add'); // "Add"
        

```