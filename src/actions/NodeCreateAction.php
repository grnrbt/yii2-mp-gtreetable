<?php
/**
 * @link https://github.com/gilek/yii2-gtreetable
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/gilek/yii2-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable\actions;

use grnrbt\yii2\gtreetable\models\TreeBehavior;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Html;

class NodeCreateAction extends ModifyAction
{

    public function run()
    {
        $model = new $this->treeModelName();
        $model->scenario = 'create';
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            throw new HttpException(500, current(current($model->getErrors())));
        }

        if ($model->nodeParent && !($model->relatedNode instanceof $this->treeModelName)) {
            throw new NotFoundHttpException(Yii::t('gtreetable', 'Position indicated by related ID is not exists!'));
        }

        try {
            if (is_callable($this->beforeAction)) {
                call_user_func_array($this->beforeAction, ['model' => $model]);
            }

            if ($model->nodeParent) {
                $action = $this->getInsertAction($model);
                call_user_func(array($model, $action), $model->relatedNode);
            } else {
                $model->makeRoot();
            }

            if (!$model->save(false)) {
                throw new Exception(Yii::t('gtreetable', 'Adding operation `{name}` failed!', ['{name}' => Html::encode((string)$model)]));
            }

            if (is_callable($this->afterAction)) {
                call_user_func_array($this->afterAction, ['model' => $model]);
            }

            echo Json::encode([
                'id' => $model->getPrimaryKey(),
                'name' => $model->getName(),
                'level' => $model->getLevel(),
                'type' => $model->getType()
            ]);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    protected function getInsertAction($model)
    {
        if ($model->insertPosition === TreeBehavior::POSITION_BEFORE) {
            return 'insertBefore';
        } else if ($model->insertPosition === TreeBehavior::POSITION_AFTER) {
            return 'insertAfter';
        } else if ($model->insertPosition === TreeBehavior::POSITION_FIRST_CHILD) {
            return 'prependTo';
        } else if ($model->insertPosition === TreeBehavior::POSITION_LAST_CHILD) {
            return 'appendTo';
        } else {
            throw new HttpException(500, Yii::t('gtreetable', 'Unsupported insert position!'));
        }
    }

}