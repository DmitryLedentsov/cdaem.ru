<?php

namespace common\modules\partners\commands;

use common\modules\partners\interfaces\ServiceInterface;
use Yii;
use yii\helpers\Console;
use common\modules\partners\models as models;

/**
 * Services
 * @package common\modules\partners\commands
 */
class ServicesController extends \yii\console\Controller
{
    /**
     * Service Processes Manager
     * Менеджер процессов
     *
     * Данный сценарий обрабатывает запуск и выключение определенных сервисов.
     * Есть специальная таблица, в которой ведется учет всех приобретенных сервисов.
     * Каждая запись имеет дату старта и дату завершения, а также логическое поле process.
     *
     * Исходя из даты старта и даты завершения сценарий должен включить или выключить
     * необходимый сервис. После выключения сервиса всегда указывается параметр process = 1,
     * чтобы избежать повторной обработки
     *
     * Вызов команды: php yii service/processes
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionProcesses()
    {
        $start = microtime(true);

        $processes = models\Service::findProcessesInQueue();
        $currentDate = date('Y-m-d H:i:s');
        $result = [];

        foreach ($processes as $process) {

            // Инициализируем сервис
            $service = Yii::$app->service->load($process->service);
            $service->setProcess($process);

            // ВЫКЛЮЧИТЬ
            // Все сервисы, которые должны деактивироваться в текущее время.
            if ($process->date_expire !== null && strtotime($currentDate) >= strtotime($process->date_expire)) {
                if (($e = $this->disableService($service, $process, $currentDate)) === true) {
                    $result[] = 'SUCCESS [DISABLE] [ID-' . $process->id . ']';

                    // Уведомить пользователя на email об активации услуги.
                    Yii::$app->consoleRunner->run('service/send-mail ' . $process->id . ' disable');
                } else {
                    $result[] = 'FAIL [DISABLE] [ID-' . $process->id . '] [' . $e . ']';
                }
            }
            // ВКЛЮЧИТЬ
            // Все сервисы, которые должны активироваться в текущее время.
            // Добавим резервные 10 минут, чтобы компенсировать запуск сценария раз в 10 минут
            elseif ($process->process != -1 && (strtotime($currentDate) + 60 * 10) >= strtotime($process->date_start)) {
                if (($e = $this->enableService($service, $process, $currentDate)) === true) {
                    $result[] = 'SUCCESS [ENABLE] [ID-' . $process->id . ']';

                    if ($service->isNeedRollBackProcess()) {
                        // Уведомить пользователя на email о деактивации услуги.
                        Yii::$app->consoleRunner->run('service/send-mail ' . $process->id . ' enable');
                    }
                } else {
                    $result[] = 'FAIL [ENABLE] [ID-' . $process->id . '] [' . $e . ']';
                }
            } else {
                continue;
            }
        }

        // Формирование результата
        $time = "Runtime: " . (microtime(true) - $start);
        $resultString = implode(PHP_EOL, $result);

        Yii::info('Сценарий успешно запущен' . PHP_EOL . $resultString . $time . PHP_EOL, 'services.processes');

        $this->stdout($resultString . PHP_EOL . $time . PHP_EOL);
    }

    /**
     * Service Calculate Position
     * Пересчет позиций объявлений
     *
     * Сценарий пересчитывает и обновляет позиции объявлений.
     *
     * Вызов команды: php yii service/calculate-position
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionCalculatePosition()
    {
        $start = microtime(true);
        $error = null;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $CalculatePosition = new \common\modules\partners\services\auxiliaries\CalculatePosition();
            $CalculatePosition->updateAdvertsPositions();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $error = $e->getMessage();
        }
        $time = "Runtime: " . (microtime(true) - $start);

        if (empty($error)) {
            Yii::info('SUCCESS - ' . $time, 'services.calculate-position');
        } else {
            Yii::error('FAIL - ' . $error . ' - ' . $time, 'services.calculate-position');
        }

        $this->stdout($time . PHP_EOL);
    }

    /**
     * Execute Instant Process By processId
     * Запускает в фоновом режиме выволнение мгновенного процесса активации сервиса
     *
     * Вызов команды: php yii service/execute-instant-process
     *
     * @param $processId
     * @return void
     * @throws \yii\db\Exception
     */
    public function actionExecuteInstantProcess($processId)
    {
        $start = microtime(true);
        $currentDate = date('Y-m-d H:i:s');
        $result = [];

        try {
            $process = models\Service::findProcessById($processId);

            if (!$process) {
                throw new \Exception('Process not found');
            }

            // Инициализируем сервис
            $service = Yii::$app->service->load($process->service);
            $service->setProcess($process);

            // ВКЛЮЧИТЬ
            if (($e = $this->enableService($service, $process, $currentDate)) === true) {

                // Формируем и отправляем письмо на почту
                $mailData = $service->getMailData();
                $sendEmail = $this->sendEmail(
                    ((isset($mailData['view']) && $mailData['view']) ? $mailData['view'] : 'after-enable'),
                    ((isset($mailData['subject']) && $mailData['subject']) ? $mailData['subject'] : $this->getEmailSubject($service, 'enable')),
                    $mailData['email'],
                    array_merge($mailData['data'], [
                        'service' => $service,
                        'process' => $process
                    ])
                );

                if (!$sendEmail) {
                    Yii::error('Процесс ID' . $processId . ' - Не удалось отправить EMAIL', 'services-email');
                }

                $result[] = 'SUCCESS [ENABLE] [ID-' . $processId . ']';
            } else {
                $result[] = 'FAIL [ENABLE] [ID-' . $processId . '] [' . $e . ']';
            }
        } catch (\Exception $e) {
            $result[] = 'FAIL [ID-' . $processId . '] [' . $e->getMessage() . ']';
        }

        // Формирование результата
        $resultString = implode(PHP_EOL, $result);
        $time = "Runtime: " . (microtime(true) - $start);

        Yii::info('Сценарий успешно запущен' . PHP_EOL . $resultString . ' ' . $time . PHP_EOL, 'services.processes');

        $this->stdout($resultString . PHP_EOL . $time . PHP_EOL);
    }

    /**
     * Send email By processId
     * Уведомить пользователя на email об оплате сервиса.
     *
     * @param $processId
     * @param $action
     * @return void
     */
    public function actionSendMail($processId, $action)
    {
        if ($action != 'enable' && $action != 'disable' && $action != 'payment') {
            $this->stdout('FAIL: $action incorrect' . PHP_EOL, Console::FG_RED);
            return;
        }

        try {
            $process = models\Service::findProcessById($processId, false);

            if (!$process) {
                throw new \Exception('Не найден активный процесс');
            }

            if (!$setTo = $this->getEmail($process)) {
                throw new \Exception('Email не определен');
            }

            if (!$service = Yii::$app->service->load($process->service)) {
                throw new \Exception('Сервис не инициализирован');
            }

            $template = 'after-disable';
            if ($action == 'payment') {
                $template = 'after-payment';
            } elseif ($action == 'enable') {
                $template = 'after-enable';
            }

            $sendEmail = $this->sendEmail(
                $template,
                $this->getEmailSubject($service, $action),
                $setTo,
                [
                    'service' => $service,
                    'process' => $process
                ]
            );

            if ($sendEmail) {
                Yii::info('Процесс ID' . $processId . ' - Успешно', 'services-email');
            } else {
                Yii::error('Процесс ID' . $processId . ' - Не удалось отправить EMAIL', 'services-email');

                $this->stdout('EMAIL NOT SEND' . PHP_EOL, Console::FG_YELLOW);
            }

            $this->stdout('SUCCESS' . PHP_EOL, Console::FG_GREEN);

        } catch (\Exception $e) {
            Yii::error('Процесс ID' . $processId . ' - Ошибка: [' . $e->getMessage() . ']', 'services-email');

            $this->stdout('FAIL: process ID' . $processId . $e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Отправить письмо
     *
     * @param $template
     * @param $subject
     * @param $setTo
     * @param array $data
     * @return bool
     */
    private function sendEmail($template, $subject, $setTo, array $data)
    {
        $mail = Yii::$app->getMailer();
        $mail->viewPath = '@common/mails/services';

        return $mail->compose($template, $data)
            ->setFrom(Yii::$app->getMailer()->messageConfig['from'])
            ->setTo($setTo)
            ->setSubject($subject)
            ->send();
    }

    /**
     * Включить сервис
     *
     * @param $service
     * @param $process
     * @param $currentDate
     * @return bool|string
     */
    private function enableService($service, $process, $currentDate)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (($result = $service->enable()) === true) {

                // Обновляем дату завершения работы сервиса
                // Устанавливаем время выключения такое-же как и время включения
                if (strpos($process->date_expire, '00:00:00')) {
                    $process->date_expire = str_replace('00:00:00', explode(' ', $currentDate)[1], $process->date_expire);
                }

                $process->date_start = $currentDate;
                $process->process = $service->isNeedRollBackProcess() ? models\Service::PROCESS_IMPLEMENTATION : models\Service::PROCESS_FINISHED;
                $process->save(false);

                $transaction->commit();

                return true;
            }

            if ($result) {
                throw new \Exception($result);
            }

            throw new \Exception('Failed to enable service');
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Выключить сервис
     *
     * @param ServiceInterface $service
     * @param models\Service $process
     * @param string $currentDate
     * @return bool|string
     */
    private function disableService(ServiceInterface $service, models\Service $process, string $currentDate)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            if (($result = $service->disable()) === true) {
                $process->date_expire = $currentDate;
                $process->process = models\Service::PROCESS_FINISHED;
                $process->save(false);

                $transaction->commit();

                return true;
            }

            if ($result) {
                throw new \Exception($result);
            }

            throw new \Exception('Failed to disable service "' . $service->getName() . '" - ID' . $process->id);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Получить имя
     *
     * @param $process
     * @return null
     */
    private function getName($process)
    {
        $name = null;
        if ($process->user) {
            $name = $process->user->profile->name . ' ' . $process->user->profile->surname;
        }

        return $name;
    }

    /**
     * Получить email
     *
     * @param $process
     * @return null
     */
    private function getEmail($process)
    {
        $email = null;
        if (isset($data['email'])) {
            $email = $data['email'];
        } else {
            if ($process->user) {
                $email = $process->user->email;
            }
        }

        return $email;
    }

    /**
     * Получить тему для письма
     *
     * @param $service
     * @param $action
     * @return string
     */
    private function getEmailSubject($service, $action)
    {
        if ($action == 'payment') {
            $subject = 'Оплата услуги "' . $service->getName() . '" на сайте - ' . Yii::$app->params['siteDomain'];
        } elseif ($action == 'enable') {
            $subject = 'Активация услуги "' . $service->getName() . '" на сайте - ' . Yii::$app->params['siteDomain'];
        } else {
            $subject = 'Истек срок действия услуги "' . $service->getName() . '" на сайте - ' . Yii::$app->params['siteDomain'];
        }

        return $subject;
    }
}
