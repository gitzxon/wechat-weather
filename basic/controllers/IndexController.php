<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/11
 * Time: 下午6:10
 */

namespace app\controllers;

require(__DIR__ . '/../utils/wechat.class.php');

use Yii;
use yii\web\Controller;
use Wechat;

class IndexController extends Controller
{
    /**
     * @var $wechatObj Wechat
     */
    public $wechatObj;

    /**
     * @var bool 关闭csrf验证，否则不能处理微信服务器的请求。
     */
    public $enableCsrfValidation = false;

    public function init()
    {
        parent::init();
        $this->wechatObj = $this->getWechatObj();
        $this->wechatObj->valid();
    }

    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if ($request->isPost) {
            $postStr = file_get_contents("php://input");
            $postArray = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            \Yii::info($postStr);
        }

        $wechat = $this->wechatObj;
        $type = $wechat->getRev()->getRevType();
        $wechat->getRevGeo();
        $wechat->checkAuth();

        //todo: 根据消息的type以及内容去数据库里面找，然后回复相应的内容。
        switch ($type) {
            case Wechat::MSGTYPE_TEXT:
                $this->wechatObj->text("hello, I'm wechat")->reply();
                exit;
                break;
            case Wechat::MSGTYPE_LOCATION:
                $x = $postArray['Location_X'];
                $y = $postArray['Location_Y'];
                $label = $postArray['Label'];
//                $this->wechatObj->text($x . ' | ' . $y . ' | ' . $label)->reply();

                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                $wechat->getRevGeo();
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $this->wechatObj->text("help info")->reply();
        }

    }

    /**
     * init the wechat object and valid if the request is from the wechat server
     */
    public function getWechatObj()
    {
        $local_config = Yii::$app->params['wechat'];
        $options = array(
            'token' => $local_config['token'], //填写你设定的key
            'encodingaeskey' => $local_config['encodingKey'], //填写加密用的EncodingAESKey
            'appid' => $local_config['appId'], //填写高级调用功能的app id
            'appsecret' => $local_config['appSecret'] //填写高级调用功能的密钥
        );
//        $this->wechatObj = new \Wechat($options);
        return new \Wechat($options);
    }

}