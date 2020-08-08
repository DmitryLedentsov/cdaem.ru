<?php

namespace common\modules\agency;

/**
 * Общий модуль [[Agency]]
 * Осуществляет всю работу с агенством.
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $imagesWatermarkPath = '@frontend/web/basic-images/watermark.png';

    /**
     * @var string
     */
    public $imagesPath = '@frontend/web/images';

    /**
     * @var string
     */
    public $imagesTmpPath = '@frontend/web/tmp/agency';

    /**
     * @var string
     */
    public $imagesWantPassPath = '@frontend/web/uploads/agency/wantpass';

    /**
     * @var string
     */
    public $imagesUrl = '/images';

    /**
     * @var string
     */
    public $previewImagesPath = '@frontend/web/images/thumbs';

    /**
     * @var string
     */
    public $previewImagesUrl = '/images/thumbs';

    /**
     * @var string
     */
    public $defaultImageSrc = '/basic-images/no_img.png';

    /**
     * @var string
     */
    public $imagePath = '@frontend/web/images/thumbs';

    /**
     * @var string
     */
    public $imageUrl = '/images/thumbs';

    /**
     * @var string
     */
    public $mskCityId = 4400;

    /**
     * @var string
     */
    public $imageResizeWidth = 1000;

    /**
     * @var string
     */
    public $previewImageResizeWidth = 500;
}
