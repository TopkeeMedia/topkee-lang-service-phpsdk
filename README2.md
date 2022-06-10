# 其他语言对接
## 准备工作
 + 获得对接时需要使用的APPID，APPSECRET
 + 准备好需要上传的语言配置文件，放到某个目录，比如topkee-lang-service-phpsdk项目的lang目录
 + 实现topkee-lang-service-phpsdk项目api目录下的接口
 > 基本功能参考LangSdk.php, 需要上传下载语言配置参考Command.php
## 基本功能(LangSdk.php)
  1. 调用Project::checkProject接口，判断服务端是否可用,可用继续
  2. 调用Project::getProject接口，报错，提示"APPID或APPSECRET错误"，不报错继续
  3. 调用Version::getVersion接口，获取服务端有那几个语言
  4. 调用LangKvs::exportKv接口获取语言的具体配置
  5. 加载本地语言列表到sdk，用于下一步合并(非必须，如果后面都是在服务端修改配置，这一步就不需要)
  6. 合并服务端和本地语言列表
  7. 将合并后的语言列表动态设置到你的项目里(这一步不在sdk里)
  > 1,2在sdk初始化的时候，3，4，6在getMessages(),5在loadLocalesMessages，LangSdk中所有属性的初始值作为你的语言sdk的参考,主要是$version='latest' 写死即可
  
## 上传下载功能(Command.php)
  1. 获得3个参数 'APPID', 'APPSECRET', 'PATH'(语言配置目录)
  2. 从'PATH'读取本地配置(LangSdk.loadLocalMessagesByPath)
  3. 初始化sdk
  4. 加载本地配置到sdk(LangSdk.loadLocalesMessages)
  5. 获取服务端配置(LangSdk.getServeMessages)
  6. 合并配置(LangSdk.mergMessages)
  7. 对每个语言进行上传(LangSdk.callSetLocaleMessage(true))
  8. 延迟10秒后再次获取服务端配置
  9. 合并配置(LangSdk.mergMessages)
  10. 将合并配置保存到本地

## 其他小功能
  1. Project::checkProject返回项目最后修改时间，获取一次服务端配置后，
  定时调用这个接口判断前后updated_at的值决定是否需要重新从服务器拿语言配置，可以省去一些不必要的调用