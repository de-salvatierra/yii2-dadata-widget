Widget for using hints of [Dadata](ttps://dadata.ru) service.

## Installation

### 1. Download
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run the following command:

```bash
$ composer require urichalex/yii2-dadata-widget
```

### 2. Get api key
Register at [DaData.ru](https://dadata.ru/profile/#info), and get api key.

### 3. Configure (optional)
You can setup container definitions if you do not want to enter api key in every widget.
Add following lines to your main configuration file:

```php
'container' => [
    'definitions' => [
        \urichalex\yii2Dadata\DadataWidget::class => [
            'apiKey' => 'my-dadata-api-key',
        ],
    ],
],
```

## Usage

```php
use urichalex\yii2Dadata\DadataWidget;
```

```php
<?= DadataWidget::widget([
    'model' => $model,
    'attribute' => 'inn',
    'apiKey' => 'your apiKey'
]) ?>
```
The following example will use the name property instead:
```php
<?= DadataWidget::widget([
    'name' => 'inn',
    'apiKey' => 'your apiKey'
]) ?>
```
You can also use this widget in an `yii\widgets\ActiveForm` using the `yii\widgets\ActiveField::widget()`
method, for example like this:
```php
<?= $form->field($model, 'inn')->widget(DadataWidget::class, [
    'apiKey' => 'your apiKey'
]) ?>
```

You can also specify additional model attributes (or form field names) that will be filled in automatically.
from the response from the service.
```php
<?= $form->field($model, 'inn')->widget(DadataWidget::class, [
    'apiKey' => 'your apiKey',
    'customAttributes' => [
        // key - model attribute name or form field name, value - attribute from response from service
        'city' => 'city_with_type',
        
        // Value can be an array
        'house' => [
            'attribute' => 'house_with_type', // Dadata response attribute name (response.data.house_with_type)
            'selector' => '#my_input_id', // Form field selector to write the value to
            'createHiddenField' => true, // create hidden field
            'value' => $someValue, // set hidden field value
            'fieldOptions' => [ // some field html options
                'class' => 'myClassName',
                'data-attribute' => 'value'
            ]
        ],
    ],
]) ?>
```

## Useful links

- DaData - https://dadata.ru
- jQuery plugin - https://github.com/hflabs/suggestions-jquery
- jQuery plugin options - https://confluence.hflabs.ru/pages/viewpage.action?pageId=204669097
- Hints - https://dadata.userecho.com/topics/2090


## Testing

```bash
$ ./vendor/bin/phpunit
```
