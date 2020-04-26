<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


class RequiredValidator extends BaseValidator
{

    public function validate(): bool
    {
        return !empty($this->value);
    }

    public function errorMessage(): string
    {
        return 'Field '. $this->field .' is required';
    }
}
