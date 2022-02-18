<?php

declare(strict_types=1);

namespace urichalex\yii2Dadata;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class DadataAsset extends AssetBundle
{
    public $depends = [
        JqueryAsset::class,
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@18.8.0/dist/js/jquery.suggestions.min.js'
    ];

    public $css = [
        'https://cdn.jsdelivr.net/npm/suggestions-jquery@18.8.0/dist/css/suggestions.min.css'
    ];
}
