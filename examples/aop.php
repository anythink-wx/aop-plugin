<?php

use GoSwoole\BaseServer\ExampleClass\Server\DefaultServer;
use GoSwoole\BaseServer\Server\Config\PortConfig;
use GoSwoole\BaseServer\Server\Config\ServerConfig;
use GoSwoole\Plugins\Aop\AopConfig;
use GoSwoole\Plugins\Aop\AopPlugin;
use GoSwoole\Plugins\Aop\ExampleClass\MonitorAspect;

require __DIR__ . '/../vendor/autoload.php';

class MyPort extends \GoSwoole\BaseServer\ExampleClass\Server\DefaultServerPort
{

}

//----多端口配置----
$httpPortConfig = new PortConfig();
$httpPortConfig->setHost("0.0.0.0");
$httpPortConfig->setPort(8080);
$httpPortConfig->setSockType(PortConfig::SWOOLE_SOCK_TCP);
$httpPortConfig->setOpenHttpProtocol(true);

$wsPortConfig = new PortConfig();
$wsPortConfig->setHost("0.0.0.0");
$wsPortConfig->setPort(8081);
$wsPortConfig->setSockType(PortConfig::SWOOLE_SOCK_TCP);
$wsPortConfig->setOpenHttpProtocol(true);

//---服务器配置---
$serverConfig = new ServerConfig();
$serverConfig->setWorkerNum(4);
$serverConfig->setRootDir(__DIR__ . "/../");


$server = new DefaultServer($serverConfig);
//添加端口
$server->addPort("http", $httpPortConfig, MyPort::class);//使用自定义实例
$server->addPort("ws", $wsPortConfig);//使用默认实例
//添加插件
$aopPlugin = new AopPlugin();
$aopConfig = new AopConfig($serverConfig->getVendorDir()."/go-swoole/base-server");
$aopConfig->addAspect(new MonitorAspect());
//设置插件配置
$aopPlugin->setAopConfig($aopConfig);
$server->getPlugManager()->addPlug($aopPlugin);
//添加进程
$server->addProcess("test1");
$server->addProcess("test2");//使用自定义实例
//配置
$server->configure();
//configure后可以获取实例
$test1Process = $server->getProcessManager()->getProcessFromName("test1");
$test2Process = $server->getProcessManager()->getProcessFromName("test2");
$httpPort = $server->getPortManager()->getPortFromName("http");
$wsPort = $server->getPortManager()->getPortFromName("ws");
//启动
$server->start();