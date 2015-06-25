<?php

namespace kak\widgets\summernote;

use yii\web\AssetBundle;

class SummernotePackagePluginAsset extends AssetBundle
{
    /** @var array */
    public $plugins = [];
    /** @var string */
    public $sourcePath = '@kak/widgets/summernote/assets';
    /** @var array */
    public $depends = [];

    public $cssFiles = ['browser'];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        foreach ($this->plugins as $plugin) {
            $this->js[] = 'summernote-ext-' . $plugin . '.js';

            if(in_array($plugin,$this->cssFiles)) {
                $this->css[] = 'summernote-ext-' . $plugin . '.css';
            }
        }
        parent::registerAssetFiles($view);
    }
}