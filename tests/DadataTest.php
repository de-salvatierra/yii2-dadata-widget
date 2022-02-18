<?php

declare(strict_types=1);

namespace urichalex\yii2Dadata\tests;

use yii\console\Application;
use yii\helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;
use urichalex\yii2Dadata\DadataWidget;

class DadataTest extends TestCase
{
    public function testApiTokenRequired()
    {
        $this->expectExceptionObject(new InvalidConfigException("'apiKey' is required"));
        DadataWidget::widget([
            'name' => 'company_address',
        ]);
    }

    public function testWrongType()
    {
        $this->expectExceptionObject(new InvalidConfigException("Wrong 'type' parameter"));
        DadataWidget::widget([
            'name' => 'company_address',
            'apiKey' => 'dadata-api-key',
            'type' => 'wrongType'
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectType()
    {
        DadataWidget::widget([
            'name' => 'company_address',
            'apiKey' => 'dadata-api-key',
            'type' => DadataWidget::TYPE_EMAIL
        ]);
    }

    public function testSetTokenByDefinitions()
    {
        $token = 'dadata-api-key';
        \Yii::$app->setContainer([
            'definitions' => [
                DadataWidget::class => [
                    'apiKey' => $token,
                ]
            ]
        ]);

        $widget = \Yii::createObject([
            'class' => DadataWidget::class,
            'name' => 'dadata-widget-name',
        ]);
        $this->assertEquals($token, $widget->apiKey);
    }

    public function testWidget()
    {
        $actual = DadataWidget::widget([
            'id' => 'widget-id',
            'name' => 'company_address',
            'apiKey' => 'dadata-api-key'
        ]);
        $expected = '<input type="text" id="widget-id" class="form-control" name="company_address">';
        $this->assertEquals($expected, $actual);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $config = [
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'runtimePath' => __DIR__ . '/runtime',
            'aliases' => [
                '@web' => '/',
                '@webroot' => '@runtime',
                '@bower' => '@vendor/bower-asset',
                '@npm'   => '@vendor/npm-asset',
            ],
        ];
        if (file_exists(__DIR__ . '/configs/config.php')) {
            $config = ArrayHelper::merge($config, include __DIR__ . '/configs/config.php');
        }
        new Application($config);
    }

    protected function tearDown(): void
    {
        \Yii::$app = null;
        parent::tearDown();
    }
}
