<?php

$wechat = require(__DIR__ . '/WechatConfig.php');
$smartWeather = require(__DIR__ . '/SmartWeatherConfig.php');
$amapMap = require(__DIR__ . '/MapConfig.php');

return [
    'adminEmail' => 'admin@example.com',
    'wechat' => $wechat,
    'smartWeather' => $smartWeather,
];
