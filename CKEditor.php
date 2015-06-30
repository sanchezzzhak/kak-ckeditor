<?php
namespace kak\widgets\ckeditor;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class CKEditor extends InputWidget
{
    /** @var array */
    private $defaultOptions = ['class' => 'form-control'];
    /** @var array */
    private $defaultClientOptions = [
        /*'height' => 400,
        'toolbarGroups' => [
            ['name' => 'document', 'groups' => ['mode', 'document', 'doctools']],
            ['name' => 'clipboard', 'groups' => ['clipboard', 'undo']],
            ['name' => 'editing', 'groups' => [ 'find', 'selection', 'spellchecker']],
            ['name' => 'forms'],
            '/',
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'colors','cleanup']],
            ['name' => 'paragraph', 'groups' => [ 'list', 'indent', 'blocks', 'align', 'bidi' ]],
            ['name' => 'links'],
            ['name' => 'insert'],
            '/',
            ['name' => 'styles'],
            ['name' => 'blocks'],
            ['name' => 'colors'],
            ['name' => 'tools'],
            ['name' => 'others'],
        ],*/
    ];

    /** @var array */
    public $options = [];
    /** @var array */
    public $clientOptions = [];
    /** @var array */
    public $plugins = [];

    public $browser  = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options       = ArrayHelper::merge($this->defaultOptions, $this->options);
        $this->clientOptions = ArrayHelper::merge($this->defaultClientOptions, $this->clientOptions);

        if($browserUrl = ArrayHelper::getValue(Yii::$app->params,'ckeditor.browser.url',false)){
            $this->clientOptions['filebrowserBrowseUrl'] = Url::to($browserUrl);
            $this->clientOptions['filebrowserUploadUrl'] = Url::to($browserUrl);
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerAssets();

        echo $this->hasModel()
            ? Html::activeTextarea($this->model, $this->attribute, $this->options)
            : Html::textarea($this->name, $this->value, $this->options);

        $clientOptions = empty($this->clientOptions) ? '{}'  : Json::encode($this->clientOptions);

        $id = $this->options['id'];
        $this->getView()->registerJs("CKEDITOR.replace('{$id}', {$clientOptions})");

    }
    private function registerAssets()
    {
        $packagePlugins = null;
        if($this->browser) {
            $packagePlugins[] = 'browser';
        }

        $view = $this->getView();
        CKEditorAsset::register($view);
/*
        if (!empty($this->plugins) && is_array($this->plugins)) {
            CKEditorPackagePluginAsset::register($view)->plugins = $packagePlugins;
        }*/
    }




} 