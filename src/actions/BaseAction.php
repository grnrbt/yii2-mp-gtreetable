<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable\actions;

use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;

abstract class BaseAction extends Action
{

    public $treeModelName;
    public $beforeRun;
    public $afterRun;
    public $beforeAction;
    public $afterAction;

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    protected function beforeRun()
    {
        if (is_callable($this->beforeRun)) {
            return call_user_func($this->beforeRun);
        }
        return parent::beforeRun();
    }

    protected function afterRun()
    {
        if (is_callable($this->afterRun)) {
            return call_user_func($this->afterRun);
        }
        parent::afterRun();
    }

    public function getNodeById($id, $with = [])
    {
        $model = (new $this->treeModelName)->find()->andWhere(['id' => $id])->with($with)->one();
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('gtreetable', 'Position is not exists!'));
        }
        return $model;
    }

    public function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['gtreetable'])) {
            Yii::$app->i18n->translations['gtreetable'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/../messages',
            ];
        }
    }

}
