Модуль Отзывы
=============
Базовый модуль отзывов.

Возможности
===========



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'reviews' => [
        'class' => 'common\modules\reviews\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace frontend\modules\reviews;

use Yii;

/**
 * Reviews module bootstrap class.
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
    'frontend\modules\reviews\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/reviews/migrations
```