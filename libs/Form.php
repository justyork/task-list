<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Libs;


class Form
{

    public function __construct($options = [])
    {
        if (!$options['method'])
            $options['method'] = 'post';

        echo Html::openTag('form', $options);
    }

    public function field($model, $field)
    {
        return new FormField($model, $field);
    }


    public function end()
    {
        echo Html::closeTag('form');
    }
}

