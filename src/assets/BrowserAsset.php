<?php

/**
 * @link https://github.com/grnrbt/yii2-mp-gtreetable
 * @copyright Copyright (c) 2016 Artur Krotov <artur@greenrabbit.ru>
 * @copyright Copyright (c) 2015 Maciej Kłak
 * @license https://github.com/grnrbt/yii2-mp-gtreetable/blob/master/LICENSE
 */

namespace grnrbt\yii2\gtreetable\assets;

class BrowserAsset extends \yii\web\AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/jquery.browser/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'jquery.browser.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
