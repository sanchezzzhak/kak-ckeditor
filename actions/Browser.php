<?php
namespace kak\widgets\summernote\actions;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\View;

class Browser extends Action
{
    public $dir = [];

    public function run()
    {



        $browserParams = ArrayHelper::getValue(Yii::$app->params,'summernode.browser',false);

        $dirs =  $browserParams['dirs'];


        $baseDir = Yii::getAlias('@webroot');
        $dir =  Yii::getAlias('@webroot');

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

        $view = new View();
        return $view->render('@kak/widgets/summernote/views/list', compact('list','dirs'));
    }


}