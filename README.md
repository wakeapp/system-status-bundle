Введение
--------

Бандл предоставляет возможность легко реализовать сбор/получение сведений о состоянии компонентов системы, например: БД, сервер, системы очередей.

Установка
---------

### Шаг 1: Загрузка бандла

Откройте консоль и, перейдя в директорию проекта, выполните следующую команду для загрузки наиболее подходящей
стабильной версии этого бандла:

```bash
    composer require wakeapp/system-status-bundle
```
*Эта команда подразумевает что [Composer](https://getcomposer.org) установлен и доступен глобально.*

### Шаг 2: Подключение бандла

После включите бандл добавив его в список зарегистрированных бандлов в `Kernel.php` файл вашего проекта:

```php
<?php 

declare(strict_types=1);

class AppKernel extends Kernel
{
    // ...

    public function registerBundles()
    {
        $bundles = [
            // ...

            new Wakeapp\Bundle\SystemStatusBundle(),
        ];

        return $bundles;
    }

    // ...
}
```

Конфигурация
------------

Чтоб начать обращаться к апи бандла для получения информации о статусе компонентов, необходимо определить апи ключ (необязательно).
```yaml
system_status:
    api_key: '%env(SYSTEM_STATUS_KEY)%'
```

Зарегистрировать апи роуты:
```yaml
  system_status_bundle:
    resource: '@SystemStatusBundle/Resources/config/routes.yaml'
```

Использование
-------------

Чтобы начать пользоваться бандлом, необходимо реализовать два интерфейса [SystemStatusProviderInterface](./Behaviour/SystemStatusProviderInterface) и [SystemStatusPartProviderInterface](./Behaviour/SystemStatusPartProviderInterface). 
#### SystemStatusProviderInterface
группа компонентов объединненых какой то общей целью (на усмотрение пользователя бандла).
```php
<?php

declare(strict_types=1);

namespace Example;

use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusProviderInterface;
use Wakeapp\Bundle\SystemStatusBundle\Enum\SystemStateEnum;

class GeneralSystemMonitoringProvider implements SystemStatusProviderInterface
{
    public const NAME = 'general';

    public function getComponentName(): string
    {
        return self::NAME;
    }

    public static function getScoreMapping(): array
    {
        return [
            SystemStateEnum::GREAT => [
                'color' => 'green',
                'limits' => [100, 91]
            ],
            SystemStateEnum::FINE => [
                'color' => 'yellow',
                'limits' => [90, 60]
            ],
            SystemStateEnum::WARNING => [
                'color' => 'orange',
                'limits' => [59, 30]
            ],
            SystemStateEnum::CRITICAL => [
                'color' => 'red',
                'limits' => [29, 0]
            ]
        ];
    }

    public function getDefaultState(): string
    {
        return SystemStateEnum::GREAT;
    }

    public static function getFineScore(): float
    {
        return static::getScoreMapping()[SystemStateEnum::GREAT]['limits'][0];
    }
}
```
#### SystemStatusPartProviderInterface
элемент группы компонентов `SystemStatusProviderInterface`, реализующий непосредственно логику сбора сведений о компоненте, например сервер:
```php
<?php

declare(strict_types=1);

namespace Example;

use Wakeapp\Bundle\SystemStatusBundle\Behaviour\SystemStatusPartProviderInterface;
use Throwable;

class NginxAvailabilitySystemMonitoringPartProvider implements SystemStatusPartProviderInterface
{
    public const NAME = 'nginx.availability';

    protected const ACTIVE = 'active';
    protected const CHECK_COUNT = 10;

    /**
     * {@inheritDoc}
     */
    public function check(): float
    {
        $score = 0;
        $output = [];

        for ($requestCount = 0; $requestCount < self::CHECK_COUNT; $requestCount++) {
            try {
                exec('systemctl is-active nginx.service 2>&1', $output, $resultCode);
            } catch (Throwable $exception) {
                $score -= 10;

                continue;
            }

            if ($resultCode !== 0) {
                $score -= 10;

                continue;
            }

            $response = array_pop($output);
            if ($response === self::ACTIVE) {
                $score += 10;
            }

            $output = [];
        }

        return (float)$score;
    }

    public function getPartTypeName(): string
    {
        return self::NAME;
    }

    public function getComponentName(): string
    {
        return GeneralSystemMonitoringProvider::NAME;
    }

    public function getCompleteScore(): float
    {
        return GeneralSystemMonitoringProvider::getFineScore();
    }
}
```

#### Доступные команды
`system:status component` - запускает сбор данных по выбранному компоненту (в примере используется компонент `general` реализованный выше):
```bash
bin/console system:status general
```


#### API
Формат обращения:
[host]/system/status/[component_name]?api_key=[apiKey]

Формат возвращаемой информации, пример:
```json
{
    "fineScore": "100",
    "currentScore": "50",
    "currentState": "great",
    "component": "general",
    "parts": {
        "nginx.availability": {
            "completeScore": "10",
            "currentScore": "10",
            "partType": "nginx.availability"
        }
    }
}
```

Лицензия
--------

[![license](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](./LICENSE)