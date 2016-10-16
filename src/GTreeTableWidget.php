<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable;

use grnrbt\yii2\gtreetable\assets\BrowserAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use grnrbt\yii2\gtreetable\assets\Asset;
use yii\web\JsExpression;

class GTreeTableWidget extends Widget
{

    public $options = [];
    public $htmlOptions = [];
    public $selector;
    public $columnName;
    public $assetBundle;
    public $link;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->registerTranslations();
        if ($this->columnName === null) {
            $this->columnName = Yii::t('gtreetable', 'Name');
        }

        if (!isset($routes)) {
            $routes = [];
        }

        $controller = (!isset($controller)) ? '' : $controller . '/';

        $routes = array_merge([
            'nodeChildren' => $controller . 'nodeChildren',
            'nodeCreate' => $controller . 'nodeCreate',
            'nodeUpdate' => $controller . 'nodeUpdate',
            'nodeDelete' => $controller . 'nodeDelete',
            'nodeMove' => $controller . 'nodeMove'
        ], $routes);

        $defaultOptions = [
            'source' => new JsExpression("function (id) {
        return {
            type: 'GET',
            url: URI('" . Url::to([$routes['nodeChildren']]) . "').addSearch({'id':id}).toString(),
            dataType: 'json',
            error: function(XMLHttpRequest) {
                console.log(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        }; 
    }"),
            'onSave' => new JsExpression("function (oNode) {
        return {
            type: 'POST',
            url: !oNode.isSaved() ? '" . Url::to([$routes['nodeCreate']]) . "' : URI('" . Url::to([$routes['nodeUpdate']]) . "').addSearch({'id':oNode.getId()}).toString(),
            data: {
                nodeParent: oNode.getParent(),
                nodeName: oNode.getName(),
                insertPosition: oNode.getInsertPosition(),
                related: oNode.getRelatedNodeId()
            },
            dataType: 'json',
            error: function(XMLHttpRequest) {
                console.log(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
            'onDelete' => new JsExpression("function(oNode) {
        return {
            type: 'POST',
            url: URI('" . Url::to([$routes['nodeDelete']]) . "').addSearch({'id':oNode.getId()}).toString(),
            dataType: 'json',
            error: function(XMLHttpRequest) {
                console.log(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
            'onMove' => new JsExpression("function(oSource, oDestination, position) {
        return {
            type: 'POST',
            url: URI('" . Url::to([$routes['nodeMove']]) . "').addSearch({'id':oSource.getId()}).toString(),
            data: {
                related: oDestination.getId(),
                insertPosition: position
            },
            dataType: 'json',
            error: function(XMLHttpRequest) {
                console.log(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
            'language' => Yii::$app->language,
        ];

        if($this->link) {
            $defaultOptions['onSelect'] = new JsExpression("function (oNode) {
        window.location.href = '". $this->link ."' + oNode.getId();
    }");
        }

        $this->options = !($this->options) ? $defaultOptions : ArrayHelper::merge($defaultOptions, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        return $this->render('widget');
    }

    /**
     * Register widget asset.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $assetBundle = $this->assetBundle instanceof AssetBundle ? $this->assetBundle : Asset::register($view);

        if (array_key_exists('language', $this->options) && $this->options['language'] !== null) {
            $assetBundle->language = $this->options['language'];
        }

        $selector = $this->selector === null ? '#' . $this->getId() : $this->selector;
        $options = Json::encode($this->options);

        $view->registerJs("jQuery('$selector').gtreetable($options);");

        if (array_key_exists('draggable', $this->options) && $this->options['draggable'] === true) {
            BrowserAsset::register($this->view);
            JuiAsset::register($this->view);
        }
    }

    public function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['gtreetable'])) {
            Yii::$app->i18n->translations['gtreetable'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@grnrbt/yii2/gtreetable/messages',
            ];
        }
    }

}
