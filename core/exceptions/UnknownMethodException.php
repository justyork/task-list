<?php

namespace core\exceptions;

/**
 * UnknownMethodException represents an exception caused by accessing an unknown object method.
 */
class UnknownMethodException extends \BadMethodCallException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Method';
    }
}
