<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


class EmailValidator extends BaseValidator
{

    public function validate(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function errorMessage(): string
    {
        return 'Inctorrect email';
    }
}
