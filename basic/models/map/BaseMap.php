<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/18
 * Time: 下午1:22
 */

namespace app\models\map;

use yii\base\Model;

class BaseMap extends Model
{
    public $x;
    public $y;
    public $location;
    public $baseUrl;
}