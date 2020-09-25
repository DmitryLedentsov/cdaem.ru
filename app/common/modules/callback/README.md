Модуль Обратный звонок
======================
Базовый модуль для управления информацией об обратных звонках

Возможности
===========
- Ведение и управление списком обратных звонков



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'callback' => [
        'class' => 'common\modules\callback\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\callback;

use Yii;

/**
 * Сallback module bootstrap class.
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
    'common\modules\callback\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/callback/migrations
```