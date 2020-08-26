Модуль Статьи
=============
Базовый модуль статьи.

Возможности
===========



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'articles' => [
        'class' => 'common\modules\articles\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\articles;

use Yii;

/**
 * Articles module bootstrap class.
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
    'common\modules\articles\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/articles/migrations
```