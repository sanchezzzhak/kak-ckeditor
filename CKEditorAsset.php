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
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $postfix = YII_DEBUG ? '' : '.min';

        $this->js[]  = 'ckeditor.js';
        $this->js[]  = 'adapters/jquery.js';

        parent::init();
    }
}
