<?php

/**
* @link https://github.com/gilek/yii2-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii2-gtreetable/blob/master/LICENSE
*/

namespace grnrbt\yii2\gtreetable\assets;

use Yii;

class Asset extends \yii\web\AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/bootstrap-gtreetable/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $language;
    public $minSuffix = '.min';
    
    public function registerAssetFiles($view) {
        $this->js[] = 'bootstrap-gtreetable' . (YII_ENV_DEV ? '' : $this->minSuffix) . '.js';
        $this->css[] = 'bootstrap-gtreetable' . (YII_ENV_DEV ? '' : $this->minSuffix) . '.css';

        if ($this->language !== null) {
            if($this->language != 'zh-CN') { // dirty hack for incorrect lang naming in bootstrap-gtreetable
                $this->language = substr($this->language, 0, 2);
            }

            $langFile = 'languages/bootstrap-gtreetable.' . $this->language . (YII_ENV_DEV ? '' : $this->minSuffix) . '.js';
//            var_dump(file_exists(Yii::getAlias($this->sourcePath . DIRECTORY_SEPARATOR . $langFile))); die;
            if (file_exists(Yii::getAlias($this->sourcePath . DIRECTORY_SEPARATOR . $langFile))) {
                $this->js[] = $langFile;
            }
        }
//var_dump($this->js); die;
        parent::registerAssetFiles($view);
    }

}
