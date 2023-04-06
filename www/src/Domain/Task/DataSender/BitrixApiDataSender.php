<?php

declare(strict_types=1);

namespace App\Domain\Task\DataSender;

use App\Domain\Distance\TaskDistance;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\SharedContext\BrowserDriver\Request;
use App\Domain\SharedContext\BrowserDriver\Response;
use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;

class BitrixApiDataSender implements DataSender
{

    private const BITRIX_AUTH_TYPE = CURLAUTH_BASIC;
    private static $BITRIX_USER;
    private static $BITRIX_SECRET;

    /**
     * @var BrowserSessionFactory
     */
    private $browserSessionFactory;

    /**
     * @var LogRequestRepository
     */
    private $logRequestRepository;

    /**
     * @var int
     */
    private $userId;


    public function __construct(BrowserSessionFactory $browserSessionFactory, LogRequestRepository $logRequestRepository, int $userId)
    {
        $this->browserSessionFactory = $browserSessionFactory;
        $this->logRequestRepository = $logRequestRepository;
        $this->userId = $userId;

        $config = require $_SERVER['DOCUMENT_ROOT'] . '/../app/1c/config.php';
        $routes = require $_SERVER['DOCUMENT_ROOT'] . '/../app/1c/routes.php';

        foreach ($routes as $k => $v) {
          $this->$k = "{$config['host']}:{$config['port']}/{$config['schema']}$v";
        }

        self::$BITRIX_USER = $config['user'];
        self::$BITRIX_SECRET = $config['password'];

    }

    public function sendTaskDistance(TaskDistance $taskDistance): Response
    {
      $arrayDistance = $taskDistance->jsonSerialize();

      $browserSession = $this->browserSessionFactory->createCurlBrowserSession();
      $response = $browserSession->request(
        Request::make(
          RequestMethodInterface::METHOD_POST,
          $this->BITRIX_HOTS_DISTANCE,
          $arrayDistance,
          [],
          [
            CURLOPT_HTTPAUTH => self::BITRIX_AUTH_TYPE,
            CURLOPT_USERPWD => sprintf('%s:%s', self::$BITRIX_USER, self::$BITRIX_SECRET),
          ]
        )
      );

      $this->logRequestRepository->log(LogRequest::createFromArray(
        date("Y-m-d H:i:s"),
        $this->userId,
        $_SERVER['REQUEST_URI'],
        json_encode($arrayDistance),
        json_encode($response)
      ));

      if ($response->status !== StatusCodeInterface::STATUS_OK) {
        throw new \Exception(sprintf('Wrong status code %d from request %s', $response->status, $response->url));
      }

      return $response;
    }

    public function sendTaskStatus(array $data, string $type): ?Response
    {
      if ($type === 'task') if (!in_array($data['status'], ['start', 'pause', 'finish'])) return false;
      if ($type === 'order') if (!in_array($data['status'], ['draft', 'queued', 'process', 'done', 'failed'])) return false;

      $url = $type === 'task' ? $this->BITRIX_HOTS_TASKS_STATUS : $this->BITRIX_HOTS_TASKS_ORDERS_STATUS;

      $browserSession = $this->browserSessionFactory->createCurlBrowserSession();
      $response = $browserSession->request(
        Request::make(
          RequestMethodInterface::METHOD_POST,
          $url,
          $data,
          [],
          [
            CURLOPT_HTTPAUTH => self::BITRIX_AUTH_TYPE,
            CURLOPT_USERPWD => sprintf('%s:%s', self::$BITRIX_USER, self::$BITRIX_SECRET),
          ]
        )
      );

      $this->logRequestRepository->log(LogRequest::createFromArray(
        date("Y-m-d H:i:s"),
        $this->userId,
        $_SERVER['REQUEST_URI'],
        json_encode($data),
        json_encode($response)
      ));

      if ($response->status !== StatusCodeInterface::STATUS_OK) {
        throw new \Exception(sprintf('Wrong status code %d from request %s', $response->status, $response->url));
      }

      return $response;
    }

    public function sendOdometerValue(array $data): ?Response
    {
      $browserSession = $this->browserSessionFactory->createCurlBrowserSession();
      $response = $browserSession->request(
        Request::make(
          RequestMethodInterface::METHOD_POST,
          $this->BITRIX_HOTS_ODOMETER_VALUE,
          $data,
          [],
          [
            CURLOPT_HTTPAUTH => self::BITRIX_AUTH_TYPE,
            CURLOPT_USERPWD => sprintf('%s:%s', self::$BITRIX_USER, self::$BITRIX_SECRET),
          ]
        )
      );

      $this->logRequestRepository->log(LogRequest::createFromArray(
        date("Y-m-d H:i:s"),
        $this->userId,
        $_SERVER['REQUEST_URI'],
        json_encode($data),
        json_encode($response)
      ));

      if ($response->status !== StatusCodeInterface::STATUS_OK) {
        throw new \Exception(sprintf('Wrong status code %d from request %s', $response->status, $response->url));
      }

      return $response;
    }

    public function sendTaskWeight(array $data): ?Response
    {
      $browserSession = $this->browserSessionFactory->createCurlBrowserSession();
      $response = $browserSession->request(
        Request::make(
          RequestMethodInterface::METHOD_POST,
          $this->BITRIX_HOTS_TASKS_WEIGHT,
          $data,
          [],
          [
            CURLOPT_HTTPAUTH => self::BITRIX_AUTH_TYPE,
            CURLOPT_USERPWD => sprintf('%s:%s', self::$BITRIX_USER, self::$BITRIX_SECRET),
          ]
        )
      );

      $this->logRequestRepository->log(LogRequest::createFromArray(
        date("Y-m-d H:i:s"),
        $this->userId,
        $_SERVER['REQUEST_URI'],
        json_encode($data),
        json_encode($response)
      ));

      if ($response->status !== StatusCodeInterface::STATUS_OK) {
        throw new \Exception(sprintf('Wrong status code %d from request %s', $response->status, $response->url));
      }

      return $response;
    }
}
