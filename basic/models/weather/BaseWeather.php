<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/14
 * Time: 下午12:07
 */

namespace app\models\weather;

use yii\base\Model;

class BaseWeather extends Model
{
    public $url;
    public $weatherData;
}