<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Core;


use Core\Validator\Validator;
use Greg\Orm\Model;

class BaseModel extends Model
{
    protected $validator;
    protected $errors = [];
    private $fieldsToValidate = [];


    protected function rules(): ?array
    {
        return null;
    }

    public function attributeLabels(): array
    {
        return  [];
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $rules = $this->rules();
        if (!$rules) return true;

        foreach ($rules as $rule) {
            $fields = $this->parseFields($rule[0]);
            $validatorClass = $this->getValidator($rule[1]);

            if (isset($rule['validator']))
                $validatorClass = $rule['validator'];

            $this->validateFields($fields, $validatorClass, $rule['message'] ?? null);
        }

        return !$this->hasErrors();

    }

    /**
     * @param $fields
     * @param $validatorClass
     * @param null $message
     */
    private function validateFields($fields, $validatorClass, $message = null)
    {
        foreach ($fields as $field) {
            $validator = new $validatorClass($field, $this->fieldsToValidate[$field]);
            if (!$validator->validate()) {
                $this->addError($field,  $message ?? $validator->errorMessage());
            }
        }
    }


    /**
     * @param $field
     * @param $error
     */
    public function addError($field, $error): void
    {
        $this->errors[$field] = $error;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function getValidator(string $validatorName): string
    {
        $validatorName = ucfirst($validatorName).'Validator';
        return "Core\Validator\\$validatorName";
    }

    /**
     * @param $raw
     * @return array
     */
    private function parseFields($raw): array
    {
        if (is_array($raw))
            return  $raw;
        elseif (is_string($raw))
            return  [$raw];
    }

    public function __construct()
    {
        $connection = DB::manager();
        parent::__construct($connection);
    }

    public function name(): string
    {
        return strtolower($this->modelName());
    }

    /**
     * @param array $record
     * @return bool|Model
     */
    public function create(array $record = [])
    {
        $create = $this->new($record);
        $this->fieldsToValidate = $record;
        if ($this->validate()) {
            return $create->save();
        }

        return false;
    }


    /**
     * @param array $columns
     * @return int
     * @throws \Exception
     */
    public function update(array $columns = []): int
    {
        [$sql, $params] = $this->setValues($columns)->toSql();

        $this->fieldsToValidate = $columns;
        if ($this->validate())
            return $this->connection()->sqlExecute($sql, $params);

        return false;
    }

    /**
     * @return int
     * @throws \Greg\Orm\SqlException
     */
    public function countAll()
    {
        return DB::manager()
            ->select()
            ->from($this->name())
            ->fetchCount();
    }

    /**
     * @return mixed
     */
    public function modelName()
    {
        return end(explode('\\', static::class));
    }

    /**
     * @return array
     */
    public function postValues()
    {
        return $this->fieldsToValidate;
    }
}
