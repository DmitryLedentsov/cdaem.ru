Модуль Мерчант
==============
Базовый модуль мерчант.

Возможности
===========




Конфигурация
============

Добавить конфигурацию в секцию modules:

```
'modules' => [
    'merchant' => [
        'class' => 'common\modules\merchant\Module',
    ]
]
```

и создать bootstrap файл для нужного приложения:

```
namespace common\modules\merchant;

use Yii;

/**
 * Merchant module bootstrap class.
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




**Запуск миграций:**

```
yii migrate --migrationPath=@common/modules/merchant/migrations
```