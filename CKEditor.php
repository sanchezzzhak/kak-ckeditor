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
        'height' => 200,
        'codemirror' => [
            'theme' => 'monokai',
        ],
        'toolbar' => [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen', 'codeview']],
            ['help', ['help']]
        ],
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

        $browserParams = ArrayHelper::getValue(Yii::$app->params,'ckeditor.browser',false);

        if($this->browser && $browserParams) {
            // $this->clientOptions['toolbar'][] = ['group', ['browser']];
            // $this->options['data-browser-url'] = Url::to($browserParams['url']);
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
        $clientOptions = empty($this->clientOptions)
            ? null
            : Json::encode($this->clientOptions);

       // $this->getView()->registerJs('jQuery( "#' . $this->options['id'] . '" ).summernote(' . $clientOptions . ');');

    }
    private function registerAssets()
    {
        $packagePlugins = null;
        if($this->browser) {
            $packagePlugins[] = 'browser';
        }

    /*    $view = $this->getView();
        if (ArrayHelper::getValue($this->clientOptions, 'codemirror')) {
            CodemirrorAsset::register($view);
        }
        SummernoteAsset::register($view);
        if ($language = ArrayHelper::getValue($this->clientOptions, 'lang', null)) {
            SummernoteLanguageAsset::register($view)->language = $language;
        }

        if (!empty($this->plugins) && is_array($this->plugins)) {
            SummernotePluginAsset::register($view)->plugins = $this->plugins;
        }
        if (!empty($packagePlugins)) {
            SummernotePackagePluginAsset::register($view)->plugins = $packagePlugins;
        }
*/
    }




} 