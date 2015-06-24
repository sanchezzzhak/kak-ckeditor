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
    protected $thumbDirName = 'thumbs';

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

        $thumbsDir = Yii::getAlias(rtrim($storage,'/')) . '/'. $this->thumbDirName;

        if(!file_exists($thumbsDir)) {
            FileHelper::createDirectory($thumbsDir);
        }

        $thumbFiles = ArrayHelper::index(FileHelper::findFiles($thumbsDir), function($element){
            return pathinfo($element,PATHINFO_BASENAME);
        });

        if(!empty($action) && $this->hasMethod('action' . $action)) {
            $this->{'action'.$action}();
        }

        $handle = opendir($dir);
        $list = [];
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $dir");
        }

        while (($file = readdir($handle)) !== false) {

            if ($file === '.' || $file === '..'  || $file === $this->thumbDirName ) {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;
            $normalizePath = FileHelper::normalizePath( str_replace($baseDir,'',$path)   ,'/');
            $list[] = [
                'name' => $file,
                'dir'  => is_dir($path),
                'ext'  => pathinfo($path, PATHINFO_EXTENSION),
                'thumb' => isset($thumbFiles[$file]) ?  pathinfo($normalizePath,PATHINFO_DIRNAME) . '/' . $this->thumbDirName . '/' . $file: false,
                'path' => $normalizePath
            ];
        }
        closedir($handle);


        $urlBrowser = $browserParams['url'];

        return $this->render('list', compact('list','dirs','urlBrowser', 'storage'));
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
        $path =  Yii::$app->request->get('path');
        $storage =  Yii::$app->request->get('storage');

        if($this->checkStorage($storage) ) {

            $basePath = Yii::getAlias('@webroot');
            $fullPath = $basePath . $path;

            $dir = pathinfo($path, PATHINFO_DIRNAME);
            $basename = pathinfo($path,PATHINFO_BASENAME);

            $thumbPath  =  $basePath . rtrim($dir,'/') . '/'. $this->thumbDirName.'/' . $basename;


            $size   = getimagesize($fullPath);
            if($size!== false){
                $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
                $this->resizeImageThumbnail($fullPath, $thumbPath  ,80,80);
                $fp = fopen ($thumbPath,'r');
                $buffer = fread($fp, filesize($thumbPath));
                fclose ($fp);
                header("Content-Type: image/".$format );
                echo $buffer;
            }

        }

        Yii::$app->end();

    }

    /**
     * @param $storage
     * @return bool
     */
    public function checkStorage($storage)
    {
        $storageList = ArrayHelper::getValue(Yii::$app->params,'summernode.browser.dirs',false);
        return ($storageList && in_array($storage,$storageList));
    }




    /**
     * @param $path
     * @param $pathThumbnailFile
     * @param int $resizeWidth
     * @param int $resizeHeight
     * @return $this|\Imagine\Image\ManipulatorInterface
     */
    public function resizeImageThumbnail($path , $pathThumbnailFile,  $resizeWidth = 0, $resizeHeight = 0)
    {
        $imagine = $this->getImageDriver();
        $img = $imagine->open($path);
        return $img->thumbnail(new \Imagine\Image\Box($resizeWidth , $resizeHeight ),\Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND )
            ->save($pathThumbnailFile, ['quality' => 100]);
    }

    /**
     * @return \Imagine\Gd\Imagine|\Imagine\Gmagick\Imagine|\Imagine\Imagick\Imagine
     */
    protected function getImageDriver()
    {
        if(class_exists('Imagick',false))
            return new \Imagine\Imagick\Imagine;

        if(class_exists('Gmagick',false))
            return new \Imagine\Gmagick\Imagine;

        return new \Imagine\Gd\Imagine;
    }

    /**
     * @param $path
     * @param $pathPreviewFile
     * @param int $resizeWidth
     * @param int $resizeHeight
     * @return $this|\Imagine\Image\ManipulatorInterface
     */
    public function resizeImage($path , $pathPreviewFile , $resizeWidth = 0, $resizeHeight = 0)
    {
        $imagine = $this->getImageDriver();

        $img = $imagine->open($path);
        $size = $img->getSize();

        $width  = $size->getWidth();
        $height =  $size->getHeight();

        if( $size->getWidth() >= $size->getHeight() && $width > $resizeWidth )
        {
            $width  = $resizeWidth;
            $height = $resizeWidth * $size->getHeight() / $size->getWidth();
        }
        else if( $size->getWidth() <= $size->getHeight() && $height > $resizeHeight )
        {
            $width =  $resizeHeight *  $size->getWidth() / $size->getHeight();
            $height = $resizeHeight;
        }

        return $img->resize(new \Imagine\Image\Box($width, $height) )
            ->save($pathPreviewFile, ['quality' => 100]);

    }

}