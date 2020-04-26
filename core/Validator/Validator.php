<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core\Validator;


interface Validator
{
    public function validate(): bool;

    public function errorMessage(): string ;

}
