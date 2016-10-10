<?php


namespace grnrbt\yii2\gtreetable\models;


use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\validators\Validator;

class TreeBehavior extends Behavior
{
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';
    const POSITION_FIRST_CHILD = 'firstChild';
    const POSITION_LAST_CHILD = 'lastChild';

    public $nodeName;
    public $nameAttribute = 'title';

    /**
     * @var int immidiate parent
     */
    public $nodeParent;
    public $insertPosition;
    /**
     * @var int related element, we insert new alement before, after or into that element
     */
    public $related;

    public function getPositions()
    {
        return [
            self::POSITION_BEFORE,
            self::POSITION_AFTER,
            self::POSITION_FIRST_CHILD,
            self::POSITION_LAST_CHILD,
        ];
    }

    public function attach($owner)
    {
        $this->createValidators($owner);

        parent::attach($owner);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate($event)
    {
        $this->owner->{$this->nameAttribute} = $this->nodeName;
    }

    /**
     * @inheritdoc
     */
    private function createValidators($owner)
    {
        $rules = [
            ['nodeName', 'required', 'on' => ['create', 'move', 'update']],
            ['nodeParent', 'required', 'on' => ['create']],
            ['related', 'required', 'on' => ['create', 'move']],
            ['insertPosition', 'required', 'on' => ['create', 'move']],
            ['insertPosition', 'in', 'range' => $this->getPositions()],
        ];

        $validators = $owner->validators;
        foreach ($rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $owner, (array)$rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
    }

    public function getRelatedNode()
    {
        return $this->owner->hasOne(get_class($this->owner), ['id' => 'related']);
    }

    public function setName($nodeName)
    {
        $this->owner->{$this->nameAttribute} = $nodeName;
    }

    public function getName()
    {
        return $this->owner->{$this->nameAttribute};
    }

    /**
     * todo make it configurable
     * @return string
     */
    public function getType()
    {
        return 'default';
    }
}