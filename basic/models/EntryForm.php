<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 16/1/8
 * Time: 下午3:16
 */

namespace app\models;

use yii\base\Model;

class EntryForm extends Model
{
    public $name;
    public $email;

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email']
        ];
    }
}