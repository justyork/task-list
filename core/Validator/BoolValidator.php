<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


class BoolValidator extends BaseValidator
{

    public function validate(): bool
    {
        return is_bool($this->value) || in_array($this->value, [0, 1]);
    }

    public function errorMessage(): string
    {
        return 'Incorrect type';
    }

    public function updateValue()
    {
        return (int)$this->value;
    }


}
