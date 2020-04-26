<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


abstract class BaseValidator implements Validator
{
    protected $field;
    protected $value;

    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function updateValue()
    {
        return $this->value;
    }

}
