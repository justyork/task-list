<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 27.04.2020
 */

namespace Core\Validator;


class XssValidator extends BaseValidator
{

    public function validate(): bool
    {
        return true;
    }

    public function errorMessage(): string
    {
        return  '';
    }

    public function updateValue()
    {
        return htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8');
    }
}
