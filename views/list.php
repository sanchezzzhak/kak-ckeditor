<?php
    use \yii\helpers\ArrayHelper;
    use \yii\helpers\Url;
?>
<style>


</style>


<div class="row">
    <div class="col-sm-2">
        <?=\yii\helpers\Html::dropDownList('dir', $storage, array_flip($dirs) ,[ 'class' => 'form-control node-browser-storage'])?>
    </div>
    <div class="col-sm-10">
        <ol class="breadcrumb node-breadcrumb" dir="ltr">
            <li><a href="#"><i class="fa fa-home fa-lg fa-fw"></i> </a></li>
            <?php
            $path = '';
            foreach($breadcrumb as $item):  $path.='/'.$item;  ?>
                <li><a href="#" data-path="<?=trim($path,'/')?>"><?=$item?></a></li>
            <?php endforeach;?>
        </ol>
    </div>
</div>
<hr>
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

               <a title="file:<?=$item['name']?>"></a>
           </li>
       <?php endforeach;?>
   </ul>
</nav>


