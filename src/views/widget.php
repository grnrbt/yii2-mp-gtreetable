<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

use grnrbt\yii2\gtreetable\GTreeTableWidget;
use grnrbt\yii2\gtreetable\assets\UrlAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

UrlAsset::register($this);

/* @var GTreeTableWidget $context */
$context = $this->context;

if (isset($title)) {
    $this->title = $title;
}

if ($context->selector === null) {
    $output = [];

    $context->htmlOptions = ArrayHelper::merge([
        'id' => $context->getId()
    ], $context->htmlOptions);

    Html::addCssClass($context->htmlOptions, 'gtreetable');
    Html::addCssClass($context->htmlOptions, 'table');

    $output[] = Html::beginTag('table', $context->htmlOptions);
    $output[] = Html::beginTag('thead');
    $output[] = Html::beginTag('tr');
    $output[] = Html::beginTag('th', array('width' => '100%'));
    $output[] = $context->columnName;
    $output[] = Html::endTag('th');
    $output[] = Html::endTag('tr');
    $output[] = Html::endTag('thead');
    $output[] = Html::endTag('table');

    echo implode('', $output);
}
