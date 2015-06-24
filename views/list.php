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
        width: 60px;
        height: 60px;
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
        border: 1px solid #ddd;
    }
    nav.browsers li a:hover {
        background: #ddd;
        opacity: 0.5;
    }
    nav.browsers .ext{
        position: absolute;
        right: 2px;;
        bottom: 2px;
        z-index: 11;
    }
    nav.browsers {
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
    <?=\yii\helpers\Html::dropDownList('dir',null, $dirs ,[ 'class' => 'form-control'])?>
</div>

<nav class="browsers">
   <ul>
       <?php foreach($list as $item):?>
           <li>
               <span class="name"><?=$item['name']?></span>
               <span class="ext label label-info"><?=$item['ext']?></span>
               <span class="thumb">
                   <?php if(in_array($item['ext'],['jpg','jpeg','png']) ):
                       $urlThumb  = !$item['thumb']
                           ? Url::to(ArrayHelper::merge($urlBrowser,['action' => 'thumb', 'path' => $item['path'], 'storage' => $storage , 'r' => microtime() ]))
                           : $item['thumb'];
                       ?>
                       <img src="<?=$urlThumb?>">
                   <?php endif;?>
               </span>
               <a></a>
           </li>
       <?php endforeach;?>
   </ul>
</nav>


