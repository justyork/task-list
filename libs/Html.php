<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Libs;


class Html
{

    /** @var array Elements without end tag */
    public static $voidElements = [
        'br' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'meta' => 1,
    ];


    public static function field($tag, $name, $value, $options = [])
    {
        $inputOptions = [];
        if (isset($_POST[$name]))
            $value = $_POST[$name];
        if (!isset($options['class']))
            $options['class'] = 'form-control';

        $inputOptions['class'] = $options['class'];

        $label = '';
        if (isset($options['label'])) {
            $label = self::tag('label', $options['label']);
        }

        $input = self::tag($tag, $value, $inputOptions);

        return self::tag('fieldbox', $label.$input);
    }


    /**
     * @param $errors
     * @return string
     */
    public static function errorSummary($errors)
    {
        $text = self::tag('p', 'Необходимо исправить следующие ошибки');

        $errorsText = [];
        foreach ($errors as $field => $err) {
            foreach ($err as $message) {
                $errorsText[] = self::tag('li', $message);
            }
        }
        $text .= self::tag('ul', implode('', $errorsText));
        return self::tag('div', $text, ['class' => 'errorSummary']);
    }

    /** Render select
     * @param $name
     * @param null $selection
     * @param array $items
     * @param array $options
     * @return string
     */
    public static function selectBox(string $name, $selection = null, $items = [], $options = []): string
    {
        if (!empty($options['multiple'])) {
            return static::listBox($name, $selection, $items, $options);
        }
        $options['name'] = $name;
        $selectOptions = static::renderSelectOptions($selection, $items, $options);
        return static::tag('select', "\n" . $selectOptions . "\n", $options);
    }

    /** Multiple listbox
     * @param $name
     * @param null $selection
     * @param array $items
     * @param array $options
     * @return string
     */
    public static function listBox(string $name, $selection = null, $items = [], $options = []): string
    {
        if (!array_key_exists('size', $options)) {
            $options['size'] = 4;
        }
        if (!empty($options['multiple']) && !empty($name) && substr_compare($name, '[]', -2, 2)) {
            $name .= '[]';
        }
        $options['name'] = $name;

        $selectOptions = static::renderSelectOptions($selection, $items, $options);
        return static::tag('select', "\n" . $selectOptions . "\n", $options);
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return string
     */
    public static function textField(string $name, $value = null, $options = []): string
    {
        $options['name'] = $name;
        $options['type'] = 'text';
        $options['value'] = $value;
        return static::tag('input', $value ?? '', $options);
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return string
     */
    public static function password(string $name, $value = null, $options = []): string
    {
        $options['name'] = $name;
        $options['type'] = 'password';
        $options['value'] = $value;
        return static::tag('input', $value ?? '', $options);
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return string
     */
    public static function textarea(string $name, $value = null, $options = []): string
    {
        $options['name'] = $name;
        return static::tag('textarea', $value, $options);
    }


    /** Render tag
     * @param string $name
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function tag(string $name, string  $content = '', $options = []): string
    {
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "{$html}{$content}</$name>";
    }

    /** Render options
     * @param $selection
     * @param $items
     * @param array $tagOptions
     * @return string
     */
    protected static function renderSelectOptions($selection, $items, &$tagOptions = []): string
    {
        $empty = null;
        if (isset($tagOptions['empty'])) {
            $empty = self::tag('option', $tagOptions['empty']);
            unset($tagOptions['empty']);
        }

        $return = [$empty];
        foreach ($items as $value => $body) {
            $selected = $selection === $value ? true : null;
            $return[] = self::tag('option', $body, ['value' => $value, 'selected' => $selected ]);
        }

        return implode('', $return);
    }


    /** Render attributes
     * @param array $attributes
     * @return string
     */
    protected static function renderTagAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }

    /** Encode html
     * @param $content
     * @param bool $doubleEncode
     * @return string
     */
    public static function encode(string $content, $doubleEncode = true): string
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    public static function openTag($name, $options)
    {
        return '<'.$name.' '.static::renderTagAttributes($options).'>';
    }

    public static function closeTag($name)
    {
        return "</{$name}>";
    }


}
