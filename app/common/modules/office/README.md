Модуль Офис
===========
Базовый модуль офис, для реализации личного кабинета.

Возможности
===========



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'office' => [
        'class' => 'common\modules\office\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\office;

use Yii;

/**
 * Office module bootstrap class.
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
            ]
        );
    }
}
```

Подключить:

```
'bootstrap' => [
    'common\modules\office\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/office/migrations
```