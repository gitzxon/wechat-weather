<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/19
 * Time: 下午3:28
 */

namespace app\models\weather;

use app\models\weather\BaseLocation;

class SmartWeatherLocation extends BaseLocation
{

    public static function tableName()
    {
        /**
         * 初步验证，基础数据是常规数据的子集。所以先用常规数据的表，以后收费了再说。
         */
        return "areaid_v";
    }

    /**
     * @param $province String 对应数据表的province_cn
     * @param $city     String 对应数据表的district_cn
     * @param $district String 对应数据表的name_cn
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAreaIdFromAreaName($province, $city, $district)
    {
        $result = SmartWeatherLocation::find()
//            ->where(["like", "province_cn", $province])
//            ->where(["like", "district_cn", $city])
//            ->where(["like", "name_cn", $province])
            ->where(["province_cn" => $province, "district_cn" => $city, "name_cn" => $district])
            ->asArray()
            ->one();
        return $result;
    }

    public function getAreaNameFromAreaId($id)
    {
        // TODO: Implement getAreaNameFromAreaId() method.
    }
}