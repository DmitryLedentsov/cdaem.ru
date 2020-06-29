<?php

namespace common\modules\messages;

use yii\helpers\Html;

/**
 * Messages module bootstrap class.
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules([
                'office/dialogs' => '/messages/default/index',
                'office/dialog/<interlocutorId:\d+>' => '/messages/default/view',
                'message/<interlocutorId:\d+>' => '/messages/default/create',
                'ajax/message/<interlocutorId:\d+>' => '/messages/ajax/message',
                'ajax/delete-message/<messageId:\d+>' => 'messages/ajax/delete-message',
                'ajax/delete-conversation/<interlocutorId:\d+>' => 'messages/ajax/delete-conversation',
            ]
        );
    }
}
