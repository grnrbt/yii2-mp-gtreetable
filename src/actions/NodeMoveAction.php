<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable\actions;

use grnrbt\yii2\gtreetable\models\TreeBehavior;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Html;

class NodeMoveAction extends ModifyAction
{

    public function run($id)
    {
        $model = $this->getNodeById($id);
        $model->scenario = 'move';
        $model->load(Yii::$app->request->post(), '');
//        var_dump($model->title); die;
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

            $action = $this->getMoveAction($model);
            call_user_func(array($model, $action), $model->relatedNode);

            if (!$model->save(false)) {
                throw new Exception(Yii::t('gtreetable', 'Moving operation `{name}` failed!', ['{name}' => Html::encode((string)$model)]));
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

    protected function getMoveAction($model)
    {
        if ($model->relatedNode->isRoot() && $model->insertPosition !== TreeBehavior::POSITION_LAST_CHILD) {
            $model->makeRoot();
        }

        if ($model->insertPosition === TreeBehavior::POSITION_BEFORE) {
            return 'insertBefore';
        } else if ($model->insertPosition === TreeBehavior::POSITION_AFTER) {
            return 'insertAfter';
        } else if ($model->insertPosition === TreeBehavior::POSITION_LAST_CHILD) {
            return 'appendTo';
        } else {
            throw new HttpException(500, Yii::t('gtreetable', 'Unsupported move position!'));
        }
    }

}

?>