<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


class SafeValidator extends BaseValidator
{

    public function validate(): bool
    {
        return true;
    }

    public function errorMessage(): string
    {
        return '';
    }
}
