<?php
namespace kak\widgets\summernote\actions;
use Yii;

use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\base\Action;





class Browser extends Action
{
    protected $thumbDirName = 'thumbs';

    /** @var $view View */
    protected $view;
    /** @var $viewPath string */
    protected $viewPath = '@kak/widgets/summernote/views/';
    /** @var  $root string */
    protected $root;

    public function init()
    {
        $this->view = new View;
        parent::init();
    }

    public function run()
    {
        $browserParams = ArrayHelper::getValue(Yii::$app->params,'summernode.browser',false);
        $firstDir  = $dirs = $browserParams['dirs'];

        // change storage
        $storage = Yii::$app->request->get('storage', false );
        if(empty($storage)) {
            $storage =  array_shift($firstDir);
        }
        if(!$this->checkStorage($storage) ){
           echo 'Storage not found';
            exit;
        }


            $this->root = Yii::getAlias($storage);
        $path = Yii::$app->request->get('path','');
        $dir =   FileHelper::normalizePath($this->absPath($path));

        $action  = Yii::$app->request->get('action', null );
        if(!empty($action) && $this->hasMethod('action' . $action)) {
            $this->{'action'.$action}();
        }

        $list = $this->getFilesList($dir);

        $breadcrumb = [];
        if(!empty($path)){
            $breadcrumb  = explode(DIRECTORY_SEPARATOR,$path);
        }
        $urlBrowser = $browserParams['url'];

        return $this->render('list', compact('list','dirs','urlBrowser', 'storage','breadcrumb'));
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


    /** FS Methods */

    protected function joinPath($dir, $name)
    {
        return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    }

    protected function relPath($path)
    {
        return $path == $this->root ? '' : substr($path, strlen(rtrim($this->root, DIRECTORY_SEPARATOR)) + 1);
    }

    protected function absPath($path)
    {
        return $path == DIRECTORY_SEPARATOR ? $this->root : $this->joinPath($this->root, $path);
    }

    protected function inPath($path, $parent)
    {
        $real_path = realpath($path);
        $real_parent = realpath($parent);
        return $real_path && $real_parent ? ( $real_path === $real_parent || strpos($real_path, rtrim($real_parent, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR) === 0)  : false;
    }

    protected function getFilesList($dir)
    {
        $baseDir = FileHelper::normalizePath(Yii::getAlias('@webroot'));
        $handle = opendir($dir);
        $list = [];
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $dir");
        }

        $thumbsDir = $dir . DIRECTORY_SEPARATOR . $this->thumbDirName;
        if(!file_exists($thumbsDir)) {
            FileHelper::createDirectory($thumbsDir);
        }
        $thumbFiles = ArrayHelper::index(FileHelper::findFiles($thumbsDir), function($element){
            return pathinfo($element,PATHINFO_BASENAME);
        });

        while (($file = readdir($handle)) !== false) {

            if ($file === '.' || $file === '..'  || $file === $this->thumbDirName ) {
                continue;
            }
            $path =  $dir . DIRECTORY_SEPARATOR . $file;
            $normalizePath = str_replace('\\','/',str_replace($baseDir,'',$path));

            $ext   = pathinfo($path, PATHINFO_EXTENSION);
            $isDir = is_dir($path);

            $list[] = [
                'name' => $file,
                'type' => $isDir ? 'dir' : 'file',
                'ext'  =>  $ext,
                'icon'  => $this->getFileIcon($ext),
                'thumb' => isset($thumbFiles[$file]) ?  pathinfo($normalizePath,PATHINFO_DIRNAME) . '/' . $this->thumbDirName . '/' . $file: false,
                'path'  => $isDir ? $this->relPath($path) : $normalizePath
            ];
        }
        closedir($handle);
        return $list;

    }
    /** ====  ==== **/


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
        //


    private function getFileIcon($ext){

        $icons = [
            'fa fa-folder' => [''],
            //archive
            'fa fa-archive'   =>['7z','ace','adf','air','apk','arj','bz2','bzip','cab','d64','dmg','git','hdf','ipf','iso','fdi','gz','jar','lha','lzh','lz','lzma','pak','phar','pkg','pimp','rar','safariextz','sfx','sit','sitx','sqx','sublime-package','swm','tar','tgz','wim','wsz','xar','zip'],
            //apple
            'fa fa-apple' =>['app','ipa','ipsw','saver'],
           //fa fa-music
            'fa fa-music' =>['aac','ac3','aif','aiff','au','caf','flac','it','m4a','m4p','med','mid','mo3','mod','mp1','mp2','mp3','mpc','ned','ra','ram','oga','ogg','oma','opus','s3m','sid','umx','wav','webma','wv','xm'],
            //calendar
            'fa fa-calendar'  =>['icbu','ics'],
            //config
            'fa fa-cogs'    =>['cfg','conf','ini','htaccess','htpasswd','plist','sublime-settings','xpy'],
            //contact
            'fa fa-group'   =>['abbu','contact','oab','pab','vcard','vcf'],
            //database
            'fa fa-database'  =>['bde','crp','db','db2','db3','dbb','dbf','dbk','dbs','dbx','edb','fdb','frm','fw','fw2','fw3','gdb','itdb','mdb','ndb','nsf','rdb','sas7mdb','sql','sqlite','tdb','wdb'],
            //doc
            'fa fa-file-text'       =>['abw','doc','docm','docs','docx','dot','key','numbers','odb','odf','odg','odp','odt','ods','otg','otp','ots','ott','pages','pdf','pot','ppt','pptx','sdb','sdc','sdd','sdw','sxi','wp','wp4','wp5','wp6','wp7','wpd','xls','xlsx','xps'],
            //ebook
            'fa fa-book'     =>['aeh','azw','ceb','chm','epub','fb2','ibooks','kf8','lit','lrf','lrx','mobi','pdb','pdg','prc','xeb'],
            //email
            'fa fa-envelope'     =>['eml','emlx','mbox','msg','pst'],
            //feed
            'fa fa-rss'      =>['atom','rss'],
            //flash
            'fa fa-bolt'     =>['fla','flv','swf'],
            //linux
            'fa fa-linux'     =>['bin','deb','rpm'],
            //raw
            'fa fa-camera'       =>['3fr','ari','arw','bay','cap','cr2','crw','dcs','dcr','dnf','dng','eip','erf','fff','iiq','k25','kdc','mdc','mef','mof','mrw','nef','nrw','obm','orf','pef','ptx','pxn','r3d','raf','raw','rwl','rw2','rwz','sr2','srf','srw','x3f'],
            //script
            'fa fa-code'=>['ahk','as','asp','aspx','bat','c','cfm','clj','cmd','cpp','css','el','erb','g','hml','java','js','json','jsp','less','nsh','nsi','php','php3','pl','py','rb','rhtml','sass','scala','scm','scpt','scptd','scss','sh','shtml','wsh','xml','yml'],
            //text
            'fa fa-file-text-o'  =>['ans','asc','ascii','csv','diz','latex','log','markdown','md','nfo','rst','rtf','tex','text','txt'],
            //video
            'fa fa-film'     =>['3g2','3gp','3gp2','3gpp','asf','avi','bik','bup','divx','ifo','m4v','mkv','mkv','mov','mp4','mpeg','mpg','rm','rv','ogv','qt','smk','vob','webm','wmv','xvid'],
            //website
            'fa fa-globe'   =>['htm','html','mhtml','mht','xht','xhtml'],
            //windows
            'fa fa-windows'   =>['dll','exe','msi','pif','ps1','scr','sys'],
        ];
        foreach($icons as $iconKey => $iconSection) {
            if(in_array($ext,$iconSection)){
                return  $iconKey;
            }
        }
        return 'fa fa-file-o';
    }


}