<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Libs;


use Core\ActiveRecord;

class FormField
{

    /** @var ActiveRecord */
    protected $model;
    protected $field;

    private $label;
    private $tag;
    private $defaultInputClass = 'form-control';
    private $errorClass = 'is-invalid';
    private $successClass = 'is-valid';
    private $errorText = false;


    public function __construct($model, $field)
    {
        $this->model = $model;
        $this->field = $field;
        $this->label = $this->model->attributeLabels()[$this->field];
    }

    /**
     * @param array $options
     * @return string
     */
    public function textField($options = []): string
    {
        $this->tag = Html::textField($this->getName(), $this->getValue(), $this->fieldOptions($options));
        return $this->generate();
    }

    /**
     * @param array $options
     * @return string
     */
    public function textarea($options = []): string
    {
        $options += ['class' => $this->defaultInputClass];
        $options['rows'] = 3;
        $this->tag = Html::textarea($this->getName(), $this->getValue(), $this->fieldOptions($options));
        return $this->generate();
    }

    /**
     * @param array $options
     * @return string
     */
    public function password($options = []): string
    {
        $this->tag = Html::textField($this->getName(), '', $this->fieldOptions($options));
        return $this->generate();
    }

    /**
     * @param $value
     * @return $this
     */
    public function label($value)
    {
        $this->label = $value;
        return $this;
    }

    /**
     * @return string
     */
    private function generateLabel(): string
    {
        return $this->label ? Html::tag('label', $this->label) : '';
    }

    /**
     * @return string
     */
    private function generate(): string
    {
        $label = $this->generateLabel();
        $error = $this->errorText ? Html::tag('div', implode(', ', $this->errorText), ['class' => 'invalid-feedback']) : '';
        return Html::tag('fieldset', $label.$this->tag.$error);
    }

    /**
     * @return string
     */
    private function getName()
    {
        return $this->model->modelName().'['.$this->field.']';
    }

    /**
     * @return |null
     */
    private function getValue()
    {
        $val = $this->model->{$this->field} ?? null;
        if (!$val)
            $val = '';
        return $val;
    }

    /**
     * @param array $options
     * @return array
     */
    private function fieldOptions(array $options)
    {
        if (!isset($options['class']))
            $options['class'] = $this->defaultInputClass;

        if (isset($this->model->getErrors()[$this->field])) {
            $options['class'] .= ' '.$this->errorClass;
            $this->errorText = $this->model->getErrors()[$this->field];
        }
        elseif (!isset($this->model->hasErrors()[$this->field]) && !empty($this->model->{$this->field}))
            $options['class'] .= ' '.$this->successClass;

        return $options;
    }


}
