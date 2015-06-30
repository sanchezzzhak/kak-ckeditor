<?php

namespace kak\widgets\ckeditor;

use Yii;
use yii\web\AssetBundle;

class CKEditorAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@vendor/ckeditor/ckeditor';
    /** @var array */
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
        'kak\widgets\ckeditor\FontawesomeAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';

        $this->css[] = 'ckeditor.css';
        $this->js[]  = 'ckeditor' . $postfix . '.js';
        parent::init();
    }
}
