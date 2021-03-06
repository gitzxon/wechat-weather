<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/14
 * Time: 下午12:14
 */

namespace app\models\weather;

use \Yii;
use app\models\weather\BaseWeather;

class SmartWeather extends BaseWeather
{
    /**
     * SmartWeather天气接口, type参数定义:
     *
     * 指数:index_f(基础), index_v(常规)
     * 3 天常规预报(24 小时):forecast_f(基础), forecast_v (常规)
     */
    const TYPE_INDEX_F = "index_f";
    const TYPE_INDEX_V = "index_v";
    const TYPE_FORECAST_F = "forecast_f";
    const TYPE_FORECAST_V = "forecast_v";

    public $url = "http://open.weather.com.cn/data/";

    public $weatherData;


    public function getWeatherData($areaid = "", $type = self::TYPE_INDEX_V)
    {
        /**
         * private_key仅负责与public_key共同合成key传参，私钥不可见，客户端与服务端各存储一份;
         * public_key为不包含key在内的完整URL其它部分（此处appid为完整appid）
         */
        $configs = Yii::$app->params['smartWeather'];
        set_time_limit(0);

        $private_key = $configs['privateKey'];
        $appid = $configs['appId'];
        $appid_six = substr($appid, 0, 6);
//        $type = 'forecast_v';
        $date = date("YmdHi");
        $public_key = $this->url . "?areaid=" . $areaid . "&type=" . $type . "&date=" . $date . "&appid=" . $appid;
        $key = base64_encode(hash_hmac('sha1', $public_key, $private_key, TRUE));

        $URL = $this->url . "?areaid=" . $areaid . "&type=" . $type . "&date=" . $date . "&appid=" . $appid_six . "&key=" . urlencode($key);

        $this->weatherData = file_get_contents($URL);
        return $this->weatherData;
    }


    /**
     * 这个接口的字段没什么字面含义。
     * @param $weatherDataJsonStr
     * @return array
     */
    public function parseForecastData($weatherDataJsonStr)
    {
        $weatherJson = json_decode($weatherDataJsonStr);
        $threeDayWeather = $weatherJson->f->f1;
        $threeDayWeatherArray = array();
        foreach ($threeDayWeather as $oneDayWeather) {
            $oneDayWeatherArray = array();
            $morningWeather = $oneDayWeather->fa;
            $eveningWeather = $oneDayWeather->fb;
            $morningTemperature = $oneDayWeather->fc;
            $eveningTemperature = $oneDayWeather->fd;

            // 99 means "无"
            $morningWeather = $morningWeather == null ? "99" : $morningWeather;
            $eveningWeather = $eveningWeather == null ? "99" : $eveningWeather;

            $oneDayWeatherArray['morningWeather'] = \Yii::$app->params['smartWeather']['atmosphere'][$morningWeather];
            $oneDayWeatherArray['eveningWeather'] = \Yii::$app->params['smartWeather']['atmosphere'][$eveningWeather];
            $oneDayWeatherArray['morningTemperature'] = $morningTemperature;
            $oneDayWeatherArray['eveningTemperature'] = $eveningTemperature;
            $threeDayWeatherArray[] = $oneDayWeatherArray;
        }
        return $threeDayWeatherArray;
    }

    /**
     * 这个接口的字段没什么字面含义。
     */
    public function parseIndexData($suggestionData)
    {
        $todaySuggestionArray = array();
        $suggestionJson = json_decode($suggestionData);
        $suggestionArray = $suggestionJson->i;
        foreach ($suggestionArray as $oneSuggestion) {
            $todaySuggestionArray[] = array(
                "type" => $oneSuggestion->i2,
                "level" => $oneSuggestion->i4,
                "description" => $oneSuggestion->i5
            );
        }
        return $todaySuggestionArray;
    }
}