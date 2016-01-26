<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/11
 * Time: 下午6:10
 */

namespace app\controllers;

require(__DIR__ . '/../utils/wechat.class.php');

use app\models\map\AmapMap;
use app\models\weather\SmartWeather;
use app\models\weather\SmartWeatherLocation;
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
//                $this->wechatObj->text('hello')->reply();

                $amapObj = new AmapMap();
                $responseJson = $amapObj->reGeo($x, $y);
                Yii::info($responseJson);
                if ($responseJson['status'] == "0") {
                    $this->wechatObj->text("对不起主人，小天实在不能理解您的地理位置")->reply();
                } else {
                    $amapObj->initLocationData($responseJson);
                    $smartWeatherLocationObj = new SmartWeatherLocation();
                    $locationJson = $smartWeatherLocationObj->getAreaIdFromAreaName($amapObj->province, $amapObj->city, $amapObj->district);
                    Yii::info($locationJson, "debug_ang");
                    if ($locationJson == null) {
                        $this->wechatObj->text("help info")->reply();
                        exit;
                    } else {
                        $locationId = $locationJson['area_id'];
                        $smartWeatherObj = new SmartWeather();
                        $weatherDataJson = $smartWeatherObj->getWeatherData($locationId, SmartWeather::TYPE_FORECAST_V);
                        $suggestionDataJson = $smartWeatherObj->getWeatherData($locationId, SmartWeather::TYPE_INDEX_V);
                        $threeDayWeatherArray = $smartWeatherObj->parseForecastData($weatherDataJson);
                        $todaySuggestionArray = $smartWeatherObj->parseIndexData($suggestionDataJson);

                        Yii::info($weatherDataJson);
                        Yii::info($suggestionDataJson);

                        $replyStr = "";
                        $locationStr = $amapObj->province . '|' . $amapObj->city . '|' . $amapObj->district . "\r\n";
                        $replyStr .= $locationStr;
                        $todayWeather = $threeDayWeatherArray[0];
                        $todayWeather['eveningTemperature'];
                        $replyStr .= "白天: " . $todayWeather['morningWeather'] . ", 温度: " . $todayWeather['morningTemperature']
                            . ", 晚上: " . $todayWeather['eveningWeather'] . ", 温度: " . $todayWeather['eveningTemperature'] . "\r\n";

                        foreach ($todaySuggestionArray as $oneSuggestion) {
                            $tempStr = $oneSuggestion["type"] . ": " . $oneSuggestion["level"] . " | " . $oneSuggestion["description"] . "\r\n";
                            $replyStr .= $tempStr;
                        }
                        $replyStr .= "小天祝您身体健康,生活快乐!";
                        $this->wechatObj->text($replyStr)->reply();
                    }
                }
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