<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Models;

use Core\ActiveRecord;

class Task extends ActiveRecord
{

    public static function model($className=__CLASS__): ActiveRecord
    {
        return parent::model($className);
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'text'], 'required', 'message' => 'Поле обязательно для заполнения'],
            [['email'], 'email', 'message' => 'Некорректный формат Email адреса'],
            [['status', 'is_updated'], 'bool']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'text' => 'Текст',
        ];
    }

}
