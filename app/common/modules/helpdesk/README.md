Модуль Технической поддержки
============================
Базовый Технической поддержки.

Возможности
===========
- Создание тикетов
- Ответы на тикеты



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'helpdesk' => [
        'class' => 'common\modules\helpdesk\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\helpdesk;

use Yii;

/**
 * Helpdesk module bootstrap class.
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
                'helpdesk/ticket/<id:\d+>/answer' => 'helpdesk/default/create-answer',
                'helpdesk/ticket/<id:\d+>' => 'helpdesk/default/view',
                'helpdesk/ask' => 'helpdesk/default/index',
            ]
        );
    }
}
```

Подключить:

```
'bootstrap' => [
    'common\modules\helpdesk\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/helpdesk/migrations
```