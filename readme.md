# Yii2 Summernote widget

This project is a fork https://github.com/zelenin/yii2-summernote-widget

[Yii2](http://www.yiiframework.com) [Summernote](http://summernote.org) widget. Super simple WYSIWYG editor on Bootstrap

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

	php composer.phar require zelenin/yii2-summernote-widget "dev-master"

or add

	"kak/summernote": "dev-master"

to the require section of your composer.json

## Usage
```php    
	<?= $form->field($model, 'content')->widget(kak\widgets\summernote\Summernote::className(), [
		'clientOptions' => [
			...
		]
	]) ?>
```
or
```php
	<?= kak\widgets\summernote\Summernote::widget([
		'name' => 'editor_id',
		'clientOptions' => [
			...
		]
	]) ?>
```
See [clientOptions](http://summernote.org/#/example)


##Configuration Browser plugin (File browser for summernote)
add section array to file config\params.php
```php
//...
      'summernode' => [
            'browser' => [
                'url' => ['site/browser'], 
                'dirs' => [ 
                    'web dir ' => '@webroot'   // allow dirs
                ],
            ]
        ]
//...      
```

Create method the SiteController 
```php
public function actionBrowser()
{
      $action = new \kak\widgets\summernote\actions\Browser($this->id, $this,[]);
      return $action->run();

}
```
Or
```php
    public function actions()
    {
        return [
            'browser' => [
                'class' => '\kak\widgets\summernote\actions\Browser',
            ],
        ];
    }
```
