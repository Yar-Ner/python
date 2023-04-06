<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Providers\UserProvider;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\SharedContext\BrowserDriver\BrowserSessionFactory;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Task\DataSender\BitrixApiDataSender;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

//todo remove this class usage
abstract class Action
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $args;

    /** @var User|null */
    protected $user;

    /** @var BitrixApiDataSender */
    protected $dataSender;

    /** @var UserRepository */
    protected $userRepository;

    /** @var UserProvider */
    protected $userProvider;

    /** @var ContainerInterface */
    protected $container;

    /** @var BrowserSessionFactory */
    protected $browserSessionFactory;

  public function __construct(
      LoggerInterface $logger,
      UserRepository $userRepository,
      UserProvider $userProvider,
      ContainerInterface $container,
      BrowserSessionFactory $browserSessionFactory
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->userProvider = $userProvider;
        $this->container = $container;
        $this->browserSessionFactory = $browserSessionFactory;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->user = $this->userProvider->getUser($request);
        $userId = $this->user ? $this->user->getId() : 12;
        $logRequestRepository = $this->get(LogRequestRepository::class);
        $this->dataSender = new BitrixApiDataSender($this->browserSessionFactory, $logRequestRepository, $userId);

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     * @throws HttpBadRequestException
     */
    protected function getFormData()
    {
        $input = json_decode(file_get_contents('php://input'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
        }

        return $input;
    }

    /**
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     * @param int $statusCode
     * @return Response
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    /**
     * @param ActionPayload $payload
     * @return Response
     */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }


    protected function get(string $key)
    {
        return $this->container->get($key);
    }

    protected function getDenormalizedRequest(string $request): object
    {
        $serializer = new Serializer(
            [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new PhpDocExtractor()), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        return $serializer->denormalize(
            $this->request->getParsedBody(),
            $request,
            JsonEncoder::FORMAT
        );
    }

  protected function getDenormalizedRequestFrom1c($data, string $request): object
  {
    $serializer = new Serializer(
      [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new PhpDocExtractor()), new ArrayDenormalizer()],
      [new JsonEncoder()]
    );

    return $serializer->denormalize(
      $data,
      $request,
      JsonEncoder::FORMAT
    );
  }
}
