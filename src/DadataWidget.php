<?php

declare(strict_types=1);

namespace urichalex\yii2Dadata;

use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use yii\helpers\{ArrayHelper, Html, Json};

class DadataWidget extends InputWidget
{
    public const TYPE_NAME = 'NAME';
    public const TYPE_ADDRESS = 'ADDRESS';
    public const TYPE_PARTY = 'PARTY';
    public const TYPE_BANK = 'BANK';
    public const TYPE_EMAIL = 'EMAIL';

    /**
     * @var array Дополнительные аттрибуты модели или формы, которые будут заполняться из ответа дадаты.
     */
    public array $customAttributes = [];

    /**
     * @var string Тип подсказок:
     * NAME — ФИО;
     * ADDRESS — адреса;
     * PARTY — организации и ИП;
     * EMAIL — адрес электронной почты;
     * BANK — банковские организации.
     */
    public string $type = self::TYPE_NAME;

    /**
     * @var string|null API token
     */
    public ?string $apiKey = null;

    /**
     * @var array Массив с аргументами для инпута.
     */
    public array $inputOptions = [];

    /**
     * @var array Массив с параметрами плагина
     * @see https://confluence.hflabs.ru/pages/viewpage.action?pageId=207454318
     */
    public array $pluginOptions = [];

    public function init()
    {
        parent::init();
        if(!$this->apiKey) {
            throw new InvalidConfigException("'apiKey' is required");
        }
        if (!in_array($this->type, [self::TYPE_NAME, self::TYPE_ADDRESS, self::TYPE_BANK, self::TYPE_EMAIL, self::TYPE_PARTY])) {
            throw new InvalidConfigException("Wrong 'type' parameter");
        }
        if(!isset($this->inputOptions['id'])) {
            $this->inputOptions['id'] = $this->options['id'];
        }
        Html::addCssClass($this->inputOptions, 'form-control');
    }

    public function run()
    {
        DadataAsset::register($this->getView());
        $this->registerPlugin();
        if($this->customAttributes) {
            echo $this->renderCustomAttributes();
        }
        echo $this->renderWidget();
    }

    /**
     * Renders the SuggestionsWidget widget.
     * @return string the rendering result.
     */
    protected function renderWidget(): string
    {
        if($this->hasModel()) {
            return Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
        } else {
            return Html::textInput($this->name, $this->value, $this->inputOptions);
        }
    }

    /**
     * Формирует скрытые поля для дополнительных аттрибутов
     * @return string
     * @throws \Exception
     */
    protected function renderCustomAttributes(): string
    {
        $fields = [];
        foreach($this->customAttributes as $field => $dadataAttribute) {
            // Если $dadataAttribute - строка, значит она является аттрибутом модели или именем поля формы
            // Если массив и не указан createHiddenField, пропускаем
            if (!is_array($dadataAttribute) || empty($dadataAttribute['createHiddenField'])) {
                continue;
            }
            // Создаем скрытое поле если указано createHiddenField true
            // Если в $dadataAttribute есть массив с параметрами поля, укажем в поле
            $fieldOptions = ArrayHelper::getValue($dadataAttribute, 'fieldOptions', []);
            if($this->hasModel()) {
                $fields[] = Html::activeHiddenInput($this->model, $field, $fieldOptions);
            } else {
                $fields[] = Html::hiddenInput($field, $dadataAttribute['value'] ?? '', $fieldOptions);
            }
        }
        return implode(PHP_EOL, $fields);
    }

    /**
     * Регистрирует jQuery плагин дадаты
     */
    protected function registerPlugin(): void
    {
        // Дефолтные параметры плагина
        $clientOptions = [
            'type' => $this->type,
            'token' => $this->apiKey
        ];
        if($this->customAttributes) {
            // Если переданы дополнительные поля, формируем обработчик, который заполнит эти поля при выборе элемента
            // списка
            $clientOptions['onSelect'] = new JsExpression($this->renderOnSelect());
        }
        $options = Json::htmlEncode(ArrayHelper::merge($clientOptions, $this->pluginOptions));
        $js = "jQuery('#{$this->inputOptions['id']}').suggestions($options);";
        $this->getView()->registerJs($js);
    }

    /**
     * Генерирует JS скрипт, в котором при выборе элемента списка заполняются дополнительные поля формы/модели
     * @return string
     */
    protected function renderOnSelect(): string
    {
        $onSelectJsCallbackBody = [];
        foreach ($this->customAttributes as $field => $dadataAttribute) {
            // Формируем селектор, по которому будет выбираться поле для данного аттрибута
            if ($this->hasModel()) {
                $selector = '#' . Html::getInputId($this->model, $field);
            } else {
                $selector = "[name=$field]";
            }
            if(is_array($dadataAttribute)) {
                // Если указан селектор в массиве, используем его
                if (!empty($dadataAttribute['selector'])) {
                    $selector = $dadataAttribute['selector'];
                }
                $dadataAttribute = $dadataAttribute['attribute'];
            }
            $onSelectJsCallbackBody[] = "$(\"$selector\").val(suggestions.data.$dadataAttribute);";
        }
        $onSelectJsCallbackBody = implode(PHP_EOL, $onSelectJsCallbackBody);
        // Формируем JS функцию, в которой заполняем значения
        // https://confluence.hflabs.ru/pages/viewpage.action?pageId=207454320 onSelect
        return <<<JS
function(suggestions) {
	$onSelectJsCallbackBody
}
JS;
    }
}
