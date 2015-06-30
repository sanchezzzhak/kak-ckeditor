<?php

namespace kak\widgets\ckeditor;

use yii\web\AssetBundle;

class CKEditorPackagePluginAsset extends AssetBundle
{
    /** @var array */
    public $plugins = [];
    /** @var string */
    public $sourcePath = '@kak/widgets/ckeditor/assets';
    /** @var array */
    public $depends = [];

    public $cssFiles = ['browser'];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        foreach ($this->plugins as $plugin) {
            $this->js[] = 'ckeditor-ext-' . $plugin . '.js';

            if(in_array($plugin,$this->cssFiles)) {
                $this->css[] = 'ckeditor-ext-' . $plugin . '.css';
            }
        }
        parent::registerAssetFiles($view);
    }
}