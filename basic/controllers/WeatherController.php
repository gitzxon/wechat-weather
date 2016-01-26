<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/14
 * Time: 下午12:28
 */

namespace app\controllers;

use \Yii;
use yii\web\Controller;
use app\models\weather\SmartWeather;

class WeatherController extends Controller
{

    public function actionIndex()
    {
        /* @var SmartWeather smartWeather */
        $smartWeather = new SmartWeather();
        echo $smartWeather->getWeatherData("101010100", SmartWeather::TYPE_FORECAST_V);
    }
}