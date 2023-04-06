<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\Action;
use App\Domain\Settings\DefaultSettingsRepository;
use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Task\Request\Modify\ModifyRequest;
use App\Domain\Task\TaskAddress\TaskAddressRepository;
use App\Domain\Task\TaskOrder\TaskOrderRepository;
use App\Domain\Task\TaskRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ModifyTaskAction extends TaskAction
{
    private function checkErrors($request): array
    {
        $errors = [];

        if (!$request->vehiclesId) {
            $errors[] = 'ID машины';
        }
        if (!$request->extId) {
            $errors[] = 'Внешний ID';
        }
        if (!$request->status) {
            $errors[] = 'Статус';
        }
        if (!$request->number) {
            $errors[] = 'Номер документа';
        }
        foreach ($request->getAddresses() as $address) {
            if (!isset($address['ext_id'])) {
                $errors[] = 'Внешний ID у адреса';
            }
            if (!isset($address['name'])) {
                $errors[] = 'Имя у адреса';
            }
            if (!isset($address['type'])) {
                $errors[] = 'Тип у адреса';
            }
            if (!isset($address['address'])) {
                $errors[] = 'Адрес у адреса';
            }
            if (!isset($address['lat'])) {
                $errors[] = 'Широта у адреса';
            }
            if (!isset($address['long'])) {
                $errors[] = 'Долгота у адреса';
            }
            if (!isset($address['radius'])) {
                $errors[] = 'Радиус у адреса';
            }
            foreach ($address['orders'] as $order) {
                if (!isset($order['ext_id'])) {
                    $errors[] = 'Внешний ID у задачи';
                }
                if (!isset($order['action'])) {
                    $errors[] = 'Действие у задачи';
                }
            }
            if (isset($address['contractor']) && !isset($address['contractor']['ext_id'])) {
                $errors[] = 'Внешний ID у контрагента';
            }
        }

        return $errors;
    }

    protected function action(): Response
    {
        /** @var TaskAddressRepository $taskAddressRepository */
        $taskAddressRepository = $this->get(TaskAddressRepository::class);

        /** @var TaskOrderRepository $taskOrderRepository */
        $taskOrderRepository = $this->get(TaskOrderRepository::class);

        /** @var LogRequestRepository $logRequestRepository */
        $logRequestRepository = $this->get(LogRequestRepository::class);

        /** @var DefaultSettingsRepository $defaultSettingsRepository */
        $defaultSettingsRepository = $this->get(DefaultSettingsRepository::class);
        $geoRadius = (int) $defaultSettingsRepository->findOneByHandle('geoRadius')->getVal();

        $from1c = $this->request->getQueryParams() && (int) $this->request->getQueryParams()['from1c'] ? (int) $this->request->getQueryParams()['from1c'] : 0;
        if ($from1c) {
          $tasks = $this->request->getParsedBody();
          $tasksRespond = [];
          foreach ($tasks as $task) {
            try {
                if (isset($task['ext_id']) && isset($task['deleted']) && $task['deleted']) {
                  $tasksRespond[] = [$this->taskRepository->delete($task['ext_id']) => $task['ext_id']];
                } else {
                  $request = $this->getDenormalizedRequestFrom1c($task, ModifyRequest::class);
                  $errors = $this->checkErrors($request);
                  if ($errors != []) {
                    return $this->respondWithData(['message' => "Ошибка! Отсутствует " . implode(", ", $errors)], 400);
                  }

                  $taskId = $this->taskRepository->modify($request);
                  $taskAddressRepository->modifyArray($taskId, $request->getAddresses(), $taskOrderRepository, $geoRadius);
                  $tasksRespond[] = [$taskId => $task['ext_id']];
                }
            } catch (\Error | \Exception $e) {
                $request = $this->getDenormalizedRequestFrom1c($task, ModifyRequest::class);
                $logRequest = LogRequest::createFromArray(
                    date("Y-m-d H:i:s"),
                    $this->user->getId(),
                    $_SERVER['REQUEST_URI'],
                    json_encode($request),
                    $e->getMessage()
                );
                $logRequestId = $logRequestRepository->log($logRequest);
              $tasksRespond[] = [ 'res' => false, "message" => $e, "ext_id" => $request->extId, "logRequestId" => $logRequestId];
            }
          }
          return $this->respondWithData($tasksRespond);
        } else {
          try {
            $request = $this->getDenormalizedRequest(ModifyRequest::class);

            $taskId = (int) $this->resolveArg('id');

            if ($taskId != $request->id) return $this->respondWithData(['result' => 0, 'message' => 'Ошибка! ID в адресной строке и в теле запроса не соответствуют друг другу!']);

            $errors = $this->checkErrors($request);
            if ($errors != []) {
                return $this->respondWithData(['result' => 0, 'message' => "Ошибка! Отсутствует ".implode(", ", $errors)], 400);
            }

            $taskId = $this->taskRepository->modify($request);
            $taskAddressRepository->modifyArray($taskId, $request->getAddresses(), $taskOrderRepository, $geoRadius);

            return $this->respondWithData(['id' => $taskId]);
          } catch (\Error | \Exception $e) {
              $request = $this->getDenormalizedRequest(ModifyRequest::class);
              $logRequest = LogRequest::createFromArray(
                  date("Y-m-d H:i:s"),
                  $this->user->getId(),
                  $_SERVER['REQUEST_URI'],
                  $request,
                  $e->getMessage()
              );
            $logRequestId = $logRequestRepository->log($logRequest);
            return $this->respondWithData(['id' => $taskId, 'error' => $e, "logRequestId" => $logRequestId], 400);
          }
        }
    }
}