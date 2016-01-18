<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/18
 * Time: 下午2:51
 */
namespace app\models\map;

use app\models\map\BaseMap;

class AmapMap extends BaseMap
{

    public $baseUrl = 'http://restapi.amap.com/v3/geocode/regeo?';
    public $province;
    public $city;
    public $district;
    /**
     * 反向地理解析
     * @param $x 纬度
     * @param $y 经度
     * @return string response
     *
     */
    public function reGeo($x, $y)
    {

        $amapConfig = \Yii::$app->params['amapMap'];
        $amapKey = $amapConfig['amapKey'];
        $location = $y . ',' .  $x;
        $url = $this->baseUrl . 'key=' . $amapKey . '&location=' . $location;
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    /**
     * init the $this->province, $this->city, $this->district
     *
     * @param $responseJsonFromAmap
     */
    public function initLocationData($responseJsonFromAmap)
    {
        $addressNames = $responseJsonFromAmap['regeocode']['addressComponent'];
        $this->province = $addressNames['province'];
        if (sizeof($addressNames['city']) == 0) {
            $this->city = $this->province;
        } else {
            $this->city = $addressNames['city'];
        }
        $this->district = $addressNames['district'];
    }
}