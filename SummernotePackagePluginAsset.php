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

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        foreach ($this->plugins as $plugin) {
            $this->js[] = 'summernote-ext-' . $plugin . '.js';
        }
        parent::registerAssetFiles($view);
    }
}