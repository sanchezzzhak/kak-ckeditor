<?php
namespace kak\widgets\summernote\actions;
use Yii;

use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\View;
use yii\base\Action;

class Browser extends Action
{
    /** @var $view View */
    protected $view;
    /** @var $viewPath string */
    protected $viewPath = '@kak/widgets/summernote/views/';

    public function init()
    {
        $this->view = new View;
        parent::init();
    }

    public function run()
    {





        $browserParams = ArrayHelper::getValue(Yii::$app->params,'summernode.browser',false);
        $dirs =  $browserParams['dirs'];

        $storage = Yii::$app->request->get('storage', array_shift($dirs) );
        $action  = Yii::$app->request->get('action', null );

        $baseDir = Yii::getAlias('@webroot');
        $dir =  Yii::getAlias($storage);

        if(!empty($action) && $this->hasMethod('action' . $action)) {
            $this->{'action'.$action}();
        }

        $handle = opendir($dir);
        $list = [];
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $dir");
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            $list[] = [
                'name' => $file,
                'dir'  => is_dir($path),
                'ext'  => pathinfo($path, PATHINFO_EXTENSION),
                'path' => FileHelper::normalizePath( str_replace($baseDir,'',$path)   ,'/')
            ];
        }
        closedir($handle);


        $urlBrowser = $browserParams['url'];

        return $this->render('list', compact('list','dirs','urlBrowser'));
    }

    protected function render($view,$params = [])
    {
        return $this->view->render($this->viewPath . $view, $params);
    }

    protected function actionUpload()
    {

    }

    protected function actionThumb()
    {

        Yii::$app->end();
    }



}