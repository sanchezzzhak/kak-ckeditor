<?php

namespace kak\widgets\summernote;

use yii\web\AssetBundle;

class SummernotePluginAsset extends AssetBundle
{
    /** @var array */
    public $plugins = [];
    /** @var string */
    public $sourcePath = '@bower/summernote/plugin';
    /** @var array */
    public $depends = [
        'kak\widgets\summernote\SummernoteAsset'
    ];

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
