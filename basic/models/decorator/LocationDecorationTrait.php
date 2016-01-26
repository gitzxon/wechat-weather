<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/20
 * Time: 上午11:42
 */

namespace app\models\decorator;

trait LocationDecorationTrait
{

    /**
     * @param $strUtf8 String
     * @return string
     */
    public function removeLastCharacter($strUtf8)
    {
        return mb_substr($strUtf8, 0, mb_strlen($strUtf8) - 1, "utf-8");
    }
}