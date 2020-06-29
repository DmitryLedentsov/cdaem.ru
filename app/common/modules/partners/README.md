Модуль Доски объявлений
=======================
Базовый модуль доски объявлений.

Возможности
===========



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'adverts' => [
        'class' => 'common\modules\partners\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace frontend\modules\partners;

use Yii;

/**
 * Partners module bootstrap class.
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
    'frontend\modules\partners\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/partners/migrations
```