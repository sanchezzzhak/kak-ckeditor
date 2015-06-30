<?php

namespace kak\widgets\ckeditor;

use yii\web\AssetBundle;

class BrowserAsset extends AssetBundle
{
    /** @var array */
    public $plugins = [];
    /** @var string */
    public $sourcePath = '@kak/widgets/ckeditor/assets';
    /** @var array */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'kak\widgets\ckeditor\FontawesomeAsset',
    ];

    public $css = [
        'ckeditor-ext-browser.css'
    ];

}