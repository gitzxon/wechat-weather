<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/19
 * Time: 下午5:33
 */

namespace app\controllers;

use yii\web\Controller;
use app\models\weather\SmartWeatherLocation;
use app\models\weather\SmartWeather;
use app\models\decorator\LocationDecorationTrait;

class LocationController extends Controller
{
    use LocationDecorationTrait;

    public function actionIndex()
    {
        $province = "北京市";
        $city = "北京市";
        $district = "海淀区";
        $province = $this->removeLastCharacter($province);
        $city = $this->removeLastCharacter($city);
        $district = $this->removeLastCharacter($district);
        $smartWeatherLocationObj = new SmartWeatherLocation();
        $locationJson = $smartWeatherLocationObj->getAreaIdFromAreaName($province, $city, $district);
        if ($locationJson == null) {

        } else {
            $locationId = $locationJson['area_id'];
            $smartWeatherObj = new SmartWeather();
            $weatherDataJson = $smartWeatherObj->getWeatherData($locationId, SmartWeather::TYPE_FORECAST_V);
            $suggestionDataJson = $smartWeatherObj->getWeatherData($locationId, SmartWeather::TYPE_INDEX_V);

            echo "<pre>";
            var_dump($weatherDataJson);
            var_dump($suggestionDataJson);
            echo "<pre>";

            $threeDayWeatherArray = $smartWeatherObj->parseForecastData($weatherDataJson);
            $todaySuggestionArray = $smartWeatherObj->parseIndexData($suggestionDataJson);


            $replyStr = "";
            $todayWeather = $threeDayWeatherArray[0];
            $todayWeather['eveningTemperature'];
            $replyStr .= "白天: " . $todayWeather['morningWeather'] . ", 温度: " . $todayWeather['morningTemperature']
                . ", 晚上: " . $todayWeather['eveningWeather'] . ", 温度: " . $todayWeather['eveningTemperature'] . "\r\n";

            foreach ($todaySuggestionArray as $oneSuggestion) {
                $tempStr = $oneSuggestion["type"] . ": " . $oneSuggestion["level"] . " | " . $oneSuggestion["description"] . "\r\n";
                $replyStr .= $tempStr;
            }
            $replyStr .= "小天祝您身体健康,生活快乐!";
            echo $replyStr;
        }
    }
}