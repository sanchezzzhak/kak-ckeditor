<?php
    use \yii\helpers\ArrayHelper;
    use \yii\helpers\Url;
?>
<style>

    nav.browsers .name{
        text-overflow: ellipsis;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }
    nav.browsers .thumb {
        display: block;
        z-index: 11;
        width: 60px;
        height: 60px;
        position: absolute;
        font-size: 60px;
        text-align: center;
        color: #fe920b;
    }
    nav.browsers .thumb img {
        width: 100%;
        height: 100%;
    }

    nav.browsers li a {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;

    }
    nav.browsers li.selected {
        border: 1px solid #666;
        background: #ddd;
    }
    nav.browsers li a:hover {
        background: #ddd;
        opacity: 0.3;
    }
    nav.browsers .ext{
        position: absolute;
        right: 2px;;
        bottom: 2px;
        z-index: 11;
    }
    nav.browsers {
        margin: 10px 0;
        position:relative;
        overflow: auto;
        height: 500px;
    }

    nav.browsers ul {
        list-style: none;
        position:relative;
        width:auto;
        padding: 0;
        user-select: none;
    }
    nav.browsers li {
        width: 32%;
        min-width: 120px;
        position: relative;
        float: left;
        height: 95px;
        line-height: 20px;
        padding: 0 5px;
        margin: 0 3px 3px 0;
        border: 1px solid rgba(236, 236, 236, 0.8);
    }
</style>


<div class="row">
    <div class="col-sm-2">
        <?=\yii\helpers\Html::dropDownList('dir', $storage, array_flip($dirs) ,[ 'class' => 'form-control node-browser-storage'])?>
    </div>
    <div class="col-sm-10">
        <ol class="breadcrumb" dir="ltr">
            <li><a href="/Bootstrap-Listr-2"><i class="fa fa-home fa-lg fa-fw"></i> </a></li>
            <?php
            $path = '';
            foreach($breadcrumb as $item):  $path.='/'.$item;  ?>
                <li><a href="<?=$path?>"><?=$item?></a></li>
            <?php endforeach;?>
        </ol>
    </div>
</div>
<hr>
<nav class="browsers">
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


