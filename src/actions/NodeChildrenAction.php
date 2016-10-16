<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable\actions;

use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use yii\validators\NumberValidator;

class NodeChildrenAction extends BaseAction
{

    public function run($id)
    {
        $validator = new NumberValidator();
        $validator->integerOnly = true;
        if (!$validator->validate($id, $error)) {
            throw new HttpException(500, $error);
        }

        $query = (new $this->treeModelName)->find();

        $nodes = [];
        if ($id == 0) {
            $nodes = $query->roots()->all();
        } else {
            $parent = $query->where(['id' => $id])->one();
            if ($parent === null) {
                throw new NotFoundHttpException(Yii::t('gtreetable', 'Position indicated by parent ID is not exists!'));
            }
            $nodes = $parent->getChildren()->all();
        }
        $result = [];
        foreach ($nodes as $node) {
            $result[] = [
                'id' => $node->getPrimaryKey(),
                'name' => $node->getName(),
                'level' => $node->getLevel(),
                'type' => $node->getType()
            ];
        }
        echo Json::encode(['nodes' => $result]);
    }

}