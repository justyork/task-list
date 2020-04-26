<?php

/**
 * Created by PhpStorm.
 * User: yorks
 * Date: 03.12.2016
 * Time: 13:41
 */

namespace Core;
use Core\Validator\BaseValidator;
use Core\Validator\XssValidator;

/** Куски от yii1.1 + моя ДБ 2015г
 * Class ActiveRecord
 * @package Core
 */
abstract class ActiveRecord
{

    private $_attributes = [];                // attribute name => attribute value
    public $table_name;
    public $isNewRecord;
    public static $className;
    private $_scenario = [];
    private $_column_list = [];
    protected $_errors = [];
    protected $_relations = [];

    private $filable = [];

    public $_fk = 'id';
    private $_model;

    private $standart_rules = [
        'require',
    ];


    public static function model($className): ActiveRecord
    {
        static::$className = $className;
        return new $className(false);
    }

    public function rules()
    {
        return [];
    }

    private function setFillable()
    {
        foreach ($this->rules() as $rule) {
            if(is_array($rule[0])) {
                foreach ($rule[0] as $field) {
                    $this->filable[] = $field;
                }
            }
            elseif(is_string($rule[0]))
                $this->filable[] = $rule[0];
        }
    }

    public function hasAttribute($attr)
    {
        return isset($this->_attributes[$attr]);
    }

    public function __construct($new_record = true)
    {
        if (!$this->table_name)
            $this->table_name = strtolower($this->modelName());

        $table = $this->table_name;
        $this->isNewRecord = $new_record;

        $q = "
            SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = '{$table}'
            ORDER BY ORDINAL_POSITION
        ";
        $sth = DB::query($q);

        $cols = $sth->fetchAll();

        $new_arr = [];
        $column_list = [];
        foreach ($cols as $col) {
            $new_arr[$col['COLUMN_NAME']] = '';
            $column_list[] = $col['COLUMN_NAME'];
        }
        $this->_column_list = $column_list;
        $this->_attributes = $new_arr;

        $this->setFillable();
        $this->init();
    }


    protected function loadById($model, $field)
    {
        if (!isset($this->_relations[$model][$field]))
            $this->_relations[$model][$field] = $model::model()->findByPk($this->$field);

        return $this->_relations[$model][$field];
    }

    public function SetScenario($scenario)
    {
        $this->_scenario[] = $scenario;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $rules = $this->rules();
        $this->beforeValidate();

        if (!$rules) return true;

        foreach ($rules as $rule) {
            $fields = $this->parseFields($rule[0]);
            $validatorClass = $this->getValidator($rule[1]);

            if (isset($rule['validator']))
                $validatorClass = $rule['validator'];

            $this->validateFields($fields, $validatorClass, $rule['message'] ?? null);
        }

        $this->vilidateXss();
        return !$this->hasErrors();
    }


    private function vilidateXss()
    {
        foreach ($this->filable as $item) {
            $validator = new XssValidator($item, $this->_attributes[$item]);
            $this->_attributes[$item] = $validator->updateValue();
        }
    }

    /**
     * @param $fields
     * @param $validatorClass
     * @param null $message
     */
    private function validateFields($fields, $validatorClass, $message = null)
    {
        foreach ($fields as $field) {
            /** @var BaseValidator $validator */
            $validator = new $validatorClass($field, $this->_attributes[$field]);
            if (!$validator->validate()) {
                $this->addError($field,  $message ?? $validator->errorMessage());
            }
            $this->_attributes[$field] = $validator->updateValue();
        }
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

    public function validateRequire($attributes)
    {
        $attributes = $this->GetArrFromFields($attributes);

        foreach ($attributes as $attribute) {
            if ($this->$attribute == '' || $this->$attribute === 0) {
                $this->addError($attribute, 'Field "' . $this->attributeLabels()[$attribute] . '" can not be empty');
            }
        }

    }

    protected function GetArrFromFields($attributes)
    {
        $attributes = str_replace(' ', '', $attributes);
        return explode(',', $attributes);
    }

    public function hasErrors()
    {
        if (count($this->_errors)) return true;
        return false;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function addError($attribute, $message)
    {
        $this->_errors[$attribute][] = $message;
    }

    public function findByPk($id)
    {
        $data = DB::byPk($this->table_name, $id);
        if (!$data) return false;
        $this->SetValues($data);
        $this->afterFind();
        return $this;
    }

    public function delete()
    {
        $this->beforeDelete();
        DB::make("DELETE FROM {$this->table_name} WHERE id = :id", array('id' => $this->_attributes[$this->_fk]));
        $this->afterDelete();
    }

    public function truncate()
    {
        DB::make("TRUNCATE TABLE " . $this->table_name);
    }

    /**
     * @param string $cond
     * @param bool $params
     * @return $this|bool
     */
    public function find($cond = '', $params = false)
    {
        $q = "SELECT * FROM {$this->table_name}";


        if (is_array($cond))
            $params = $this->Parameters($cond);

        $cond = $this->Condition($cond);

        $q .= $cond;

        $data = DB::getOne($q, $params);
        if (!$data) return false;
        $this->SetValues($data);
        $this->afterFind();
        return $this;
    }

    public function countAll($cond = '', $params = false)
    {
        return $this->count($cond, $params);
    }

    public function count($cond = '', $params = false)
    {
        $q = "SELECT COUNT(*) FROM {$this->table_name}";

        if (is_array($cond))
            $params = $this->Parameters($cond);

        $cond = $this->Condition($cond);

        $q .= $cond;

        $data = DB::count($q, $params);
        return $data;
    }

    public function findAll($cond = '', $params = false)
    {
        $q = "SELECT * FROM {$this->table_name}";

        if (is_array($cond))
            $params = $this->Parameters($cond);

        $cond = $this->Condition($cond);

        $q .= $cond;

        $data = DB::select($q, $params);
        if (!$data) return false;
        $arr = [];

        foreach ($data as $item) {

            $model = new self::$className(false);
            $model->_attributes = $item;
            $model->afterFind();
            $arr[] = $model;
        }

        return $arr;
    }

    public function asArray()
    {
        return $this->_attributes;
    }

    public function findAllArray($cond = '', $params = false)
    {
        $model = $this->findAll($cond, $params);

        $new_array = [];
        foreach ($model as $item)
            $new_array[] = $item->asArray();

        return $new_array;
    }

    private function Parameters($cond)
    {
        if (isset($cond['params']))
            return $cond['params'];

        return false;
    }

    private function Condition($cond)
    {

        if (empty($cond)) return '';
        if (!is_array($cond)) return " WHERE {$cond}";

        $cond_ret = '';
        if (isset($cond['condition']))
            $cond_ret .= " WHERE {$cond['condition']}";
        if (isset($cond['order']))
            $cond_ret .= ' ORDER BY ' . $cond['order'];
        if (isset($cond['limit']))
            $cond_ret .= ' LIMIT ' . $cond['limit'];
        if (isset($cond['offset']))
            $cond_ret .= ' OFFSET ' . $cond['offset'];

        return $cond_ret;
    }

    public function SetValues($item)
    {
        $this->_attributes = $item;
    }

    public function beforeValidate()
    {
    }

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }

    public function afterFind()
    {
    }

    public function afterDelete()
    {
    }

    public function beforeDelete()
    {
    }

    public function init()
    {
    }

    public function save()
    {
        if (!$this->validate()) return false;


        $fk = $this->_fk;

        $this->beforeSave();
        $values = $this->_attributes;
        foreach ($values as $key => $value){
            if ($key === 'id') {
                if ($value) continue;
                unset($values['id'] );
            }
            if (!in_array($key, $this->filable))
                unset($values[$key]);
            elseif ($value === ''){
                unset($values[$key]);
            }
        }
        if ($this->isNewRecord) {
            if ($id = DB::insert($this->table_name, $values)) {
                $this->$fk = $id;
                $values[$fk] = $id;
                $this->afterSave();
                $this->isNewRecord = false;
                return true;
            }
        } else {
            if (DB::update($this->table_name, $values)) {
                $this->afterSave();
                return true;
            } else {

            }
        }

        return false;
    }

    public function setAttributes($post)
    {
        foreach ($post as $name => $value) {
            if (isset($this->_attributes[$name]))
                $this->_attributes[$name] = $value;
        }
    }

    public function getAttributeLabel($attribute)
    {
        return $this->getRelationLabel($attribute);
    }

    public function __get($name)
    {
        if (isset($this->_attributes[$name]))
            return $this->_attributes[$name];
        elseif (method_exists($this, 'get' . ucfirst($name))) {
            $method_name = 'get' . ucfirst($name);
            return $this->$method_name();
        }
    }


    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_attributes[$name]);
    }

    public function getRelationLabel($relationName, $n = null, $useRelationLabel = true)
    {
        // Exploding the chained relation names.
        $relNames = explode('.', $relationName);

        // Everything starts with this object.
        $relClassName = get_class($this);

        // The item index.
        $relIndex = 0;

        // Get the count of relation names;
        $countRelNames = count($relNames);

        // Walk through the chained relations.
        foreach ($relNames as $relName) {
            // Increments the item index.
            $relIndex++;

            // Get the related static class.
            $relStaticClass = self::model($relClassName);

            // If is is the last name and the label is explicitly defined, return it.
            if ($relIndex === $countRelNames) {
                $labels = $relStaticClass->attributeLabels();
                if (isset($labels[$relName]))
                    return $labels[$relName];
            }

            // Get the relations for the current class.
            $relations = $relStaticClass->relations();

            // Check if there is (not) a relation with the current name.
            if (!isset($relations[$relName])) {
                // There is no relation with the current name. It is an attribute or a property.
                // It must be the last name.
                if ($relIndex === $countRelNames) {
                    // Check if it is an attribute.
                    $attributeNames = $relStaticClass->attributeNames();
                    $isAttribute = in_array($relName, $attributeNames);
                    // If it is an attribute and the attribute is a FK and $useRelationLabel is true, return the related AR label.
                    if ($isAttribute && $useRelationLabel && (($relData = self::findRelation($relStaticClass, $relName)) !== null)) {
                        // This will always be a BELONGS_TO, then singular.
                        return self::model($relData[3])->label(1);
                    } else {
                        // There's no label for this attribute or property, generate one.
                        return $relStaticClass->generateAttributeLabel($relName);
                    }
                } else {
                    // It is not the last item.
                    throw new InvalidArgumentException(Yii::t('giix', 'The attribute "{attribute}" should be the last name.', array('{attribute}' => $relName)));
                }
            }

            // Change the current class name: walk to the next relation.
            $relClassName = $relations[$relName][1];
        }

        // Automatically apply the correct number if requested.
        if ($n === null) {
            // Get the type of the last relation from the last but one class.
            $relType = $relations[end($relNames)][0];

            switch ($relType) {
                case self::HAS_MANY:
                case self::MANY_MANY:
                    $n = 2;
                    break;
                case self::BELONGS_TO:
                case self::HAS_ONE:
                default :
                    $n = 1;
            }
        }

        // Get and return the label from the related AR.
        return self::model($relClassName)->label($n);
    }

    public function getColumns()
    {
        return $this->_column_list;
    }

    // Пересобрать атрибуты с добавлением косых ковычек
    private function SqlAttributions()
    {
        $new_attr = [];
        foreach ($this->_attributes as $key => $value) {
            $new_attr['`' . $key . '`'] = $value;
        }

        return $new_attr;
    }
    public function modelName()
    {
        return end(explode('\\', static::class));
    }
}
