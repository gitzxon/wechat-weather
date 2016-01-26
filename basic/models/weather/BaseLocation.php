<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/19
 * Time: 下午2:57
 */

namespace app\models\weather;

use yii\db\ActiveRecord;

abstract class BaseLocation extends ActiveRecord
{
    abstract public function getAreaIdFromAreaName($province, $city, $district);
    abstract public function getAreaNameFromAreaId($id);
}