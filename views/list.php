<?php
    /** @var $this \yii\web\View  */

    use \yii\helpers\ArrayHelper;
    use \yii\helpers\Url;
    use \yii\helpers\Html;
    use yii\widgets\Pjax;

    $storages = array_flip($dirs);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php \kak\widgets\ckeditor\BrowserAsset::register($this); ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container-fluid node-browser">

        <div class="panel panel-default">
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-1">
                        <div class="form-group">

                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?=isset($storages[$storage]) ?$storages[$storage]: ' undefined' ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                <?php foreach ($dirs as $dir => $dirItem):?>
                                    <li><a href="<?=Url::to(ArrayHelper::merge($urlBrowser,['path' =>trim($path,'/'), 'storage' => $dirItem  ]))?>" data-pjax="1"><?=$dir?></a></li>
                                <?php endforeach;?>
                                </ul>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-11">
                        <ol class="breadcrumb node-breadcrumb" dir="ltr">
                            <li><a href="<?=Url::to(ArrayHelper::merge($urlBrowser,['path' =>'', 'storage' => $storage  ]))?>" data-pjax="1"><i class="fa fa-home fa-lg fa-fw"></i> </a></li>
                            <?php
                            $path = '';
                            foreach($breadcrumb as $item):  $path.='/'.$item;  ?>
                                <li><a href="<?=Url::to(ArrayHelper::merge($urlBrowser,['path' =>trim($path,'/'), 'storage' => $storage  ]))?>" data-pjax="1" data-path="<?=trim($path,'/')?>"><?=$item?></a></li>
                            <?php endforeach;?>
                        </ol>
                    </div>
                </div>

                <nav class="node-browser-listing">
                    <ul>
                        <?php foreach($list as $item):?>
                            <li data-type="<?=$item['type']?>" data-path="<?=$item['path']?>">
                                <span class="name"><?=$item['name']?></span>
                                <span class="ext label label-info"><?=$item['ext']?></span>

                               <span class="thumb">
                                   <?php if(in_array($item['ext'],['jpg','jpeg','png']) ):
                                       $urlThumb  = !$item['thumb']
                                           ? Url::to(ArrayHelper::merge($urlBrowser,['action' => 'thumb', 'path' => $item['path'], 'storage' => $storage , 'r' => microtime() ]))
                                           : $item['thumb'];
                                       ?>
                                       <img src="<?=$urlThumb?>">
                                   <?php else:?>
                                       <i class="<?=$item['icon']?>"></i>
                                   <?php endif;?>
                               </span>

                                <?php if($item['type'] == 'file'):?>
                                    <a title="file:<?=$item['name']?>"></a>
                                <?php endif;?>

                                <?php if($item['type'] == 'dir'):?>
                                    <a href="<?=Url::to(ArrayHelper::merge($urlBrowser,['path' =>$item['path'], 'storage' => $storage  ]))?>" data-pjax="1" title="dir:<?=$item['name']?>"></a>
                                <?php endif;?>

                            </li>
                        <?php endforeach;?>
                    </ul>
                </nav>
            </div>
            <div class="panel-footer">
                <a class="btn btn-info note-browser-done">Select</a>
            </div>
        </div>


</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>



