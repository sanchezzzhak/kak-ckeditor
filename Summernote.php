<?php
namespace kak\widgets\summernote;
use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;

class Summernote extends InputWidget
{
    /** @var array */
    private $defaultOptions = ['class' => 'form-control'];
    /** @var array */
    private $defaultClientOptions = [
        'height' => 200,
        'codemirror' => [
            'theme' => 'monokai'
        ]
    ];
    /** @var array */
    public $options = [];
    /** @var array */
    public $clientOptions = [];
    /** @var array */
    public $plugins = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options       = ArrayHelper::merge($this->defaultOptions, $this->options);
        $this->clientOptions = ArrayHelper::merge($this->defaultClientOptions, $this->clientOptions);
        parent::init();
    }






} 