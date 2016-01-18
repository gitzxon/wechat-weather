<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/18
 * Time: 下午1:28
 */

namespace app\controllers;

use yii\web\Controller;
use app\models\map\AmapMap;

class MapController extends Controller
{
    public function actionIndex()
    {
        $amapObj = new AmapMap();
        $x = '39.993320';
        $y = '116.332764';
        $responseJson = $amapObj->reGeo($x, $y);


        echo $province . ' | ' . $city . ' | ' . $district . ' | <br>';

        echo '<pre>';
        var_dump($responseJson);
        echo '<pre>';
    }
}