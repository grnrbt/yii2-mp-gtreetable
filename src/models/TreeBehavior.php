<?php


namespace grnrbt\yii2\gtreetable\models;


use yii\base\Behavior;
use yii\helpers\Html;

class TreeBehavior extends Behavior
{
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';
    const POSITION_FIRST_CHILD = 'firstChild';
    const POSITION_LAST_CHILD = 'lastChild';

    public $nameAttribute = 'title';

    /**
     * @var int immidiate parent
     */
    public $parent;
    public $position;
    /**
     * @var int related element, we insert new alement before, after or into that element
     */
    public $related;

    public function getPositions() {
        return [
            self::POSITION_BEFORE,
            self::POSITION_AFTER,
            self::POSITION_FIRST_CHILD,
            self::POSITION_LAST_CHILD,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['parent', 'required'],
            ['related', 'required'],
            ['position', 'required'],
            ['position', 'in', 'range' => $this->getPositions()],
            [$this->nameAttribute, 'required'],
            [$this->nameAttribute, 'string', 'max' => 128],
            [$this->nameAttribute, 'filter', 'filter' => function($value) {
                return Html::encode($value);
            }, 'skipOnError' => true]
        ];
    }

    function scenarios() {
        return [
            'create' => ['parent', 'related', 'position', $this->nameAttribute],
            'update' => [$this->nameAttribute],
            'move' => ['related', 'position'],
            self::SCENARIO_DEFAULT => [],
        ];
    }

    public function getName()
    {
        return $this->owner->{$this->nameAttribute};
    }

    public function getDepth()
    {
        return $this->owner->getLevel();
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