# Yii2 ckeditor widget

[Yii2](http://www.yiiframework.com) [ckeditor](http://ckeditor.com) widget.

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

	php composer.phar require kak/ckeditor "dev-master"

or add

	"kak/ckeditor": "dev-master"

to the require section of your composer.json




## Usage
```php    
	<?= $form->field($model, 'content')->widget(kak\widgets\ckeditor\CKEditor::className(), [
		'clientOptions' => [
			...
		]
	]) ?>
```
or
```php
	<?= kak\widgets\ckeditor\CKEditor::widget([
		'name' => 'editor_id',
		'clientOptions' => [
			...
		]
	]) ?>
```
See [clientOptions](http://ckeditor.com/#/example)


##Configuration Browser plugin (File browser for ckeditor)
add section array to file config\params.php
```php
//...
      'ckeditor' => [
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
    public function actions()
    {
        return [
            'browser' => [
                'class' => '\kak\widgets\ckeditor\actions\Browser',
            ],
        ];
    }
```


