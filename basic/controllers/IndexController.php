<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/11
 * Time: 下午6:10
 */

namespace app\controllers;

use yii\web\Controller;

class IndexController extends Controller
{
    public function actionIndex()
    {
        $this->checkSignature();
    }

    private function checkSignature()
    {
        if (!(isset($_GET["signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"]) && isset($_GET["echostr"]))) {
            return false;
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];

        $token = "wechatweather";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $echostr;
        } else {
            return false;
        }
    }
}