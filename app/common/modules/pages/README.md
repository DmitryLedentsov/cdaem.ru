Модуль Статические страницы
===========================
Базовый модуль статических страниц.

Возможности
===========
- Создание и редактирование статических страниц на сайте.
- Перевод статических страниц на другие языки.



Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'pages' => [
        'class' => 'common\modules\pages\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\pages;

use Yii;

/**
 * Pages module bootstrap class.
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
                'page/<url>' => 'pages/default/index',
            ]
        );
    }
}
```

Подключить:

```
'bootstrap' => [
    'common\modules\pages\Bootstrap',
],
```

**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/pages/migrations
```