<?php

namespace common\modules\pages;

use Yii;

/**
 * Общий модуль [[Pages]]
 * Осуществляет всю работу со статическими страницами.
 */
class Module extends \yii\base\Module
{
    /**
     * @var string Image path
     */
    public $imagePath = '@frontend/web/uploads/pages';

    /**
     * @var string Files path
     */
    public $filePath = '@statics/pages/files';
    
    /**
     * @var string Files path
     */
    public $contentPath = '@statics/pages/content';
    
    /**
     * @var string Images temporary path
     */
    public $imagesTempPath = '@statics/pages/tmp';
        
    /**
     * @var string Image URL
     */
    public $imageUrl = '/uploads/pages';
    
    /**
     * @var string Files URL
     */
    public $fileUrl = '/statics/pages/files';
    
    /**
     * @var string Files URL
     */
    public $contentUrl = '/statics/pages/content';
    
	/**
	 * @var integer Количество записей на главной странице модуля.
	 */
	public $recordsPerPage = 18;

}