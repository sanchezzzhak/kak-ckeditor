<?php

namespace kak\widgets\ckeditor;

use yii\web\AssetBundle;

class FontawesomeAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/font-awesome';
    /** @var array */
    public $depends = [
        'yii\bootstrap\BootstrapAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $this->css[] = 'css/font-awesome' . $postfix . '.css';
        parent::init();
    }
}
