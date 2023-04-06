<?php


namespace App\Application\Actions\Photo;


use App\Application\Actions\Action;
use App\Domain\Chat\ChatMessage;
use App\Domain\Chat\ChatMessageRepository;
use App\Domain\Photo\Photo;
use Error;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\Photo\Request\PhotoInBase64CreateRequest;
use App\Domain\SharedContext\FileUploaderService;
use App\Domain\SharedContext\Log\LogRequest;
use App\Domain\SharedContext\Log\LogRequestRepository;
use Psr\Http\Message\ResponseInterface as Response;

class CreatePhotoAction extends Action
{

    protected function action(): Response
    {
      /** @var LogRequestRepository $logRequestRepository */
      $logRequestRepository = $this->get(LogRequestRepository::class);

      /** @var PhotoInBase64CreateRequest $photoRequest */
      $photoRequest = $this->getDenormalizedRequest(PhotoInBase64CreateRequest::class);

      try {
          if (!empty($_FILES)) {
              if (is_null($_FILES['upload'])) {
                  return $this->respondWithData([ "status" => "error"]);
              }

              $chatId = $this->request->getQueryParams()['chatId'];
              $params['userId'] = $this->user->getId();
              $params['recipientId'] = $chatId;

              $fileInfo = $this->get(FileUploaderService::class)->moveFile($_FILES['upload'], $params);

              /** @var ChatMessageRepository $repository */
              $repository = $this->get(ChatMessageRepository::class);

              $message = new ChatMessage(null, $this->user->getId(), (int)$chatId, 0, $fileInfo['infoPath'], new \DateTimeImmutable("now"), 800);
              $repository->save($message);

              return $this->respondWithData([
                  'messageId' => $message->getId(),
                  'hash' => $fileInfo['hash'],
                  'status' => 'server',
                  'value' => ''
              ]);
          } else {
              $file = $this->get(FileUploaderService::class)->uploadFileFromBase64($photoRequest);

              return $this->respondWithData([
                  'id' => $this->get(PhotoRepositoryInterface::class)->save(Photo::makeByRequest($file, $this->user->getId(), $photoRequest))
              ]);
          }

      } catch (Error|\Exception $e) {

        $logRequest = LogRequest::createFromArray(
          date("Y-m-d H:i:s"),
          $this->user->getId(),
          $_SERVER['REQUEST_URI'],
          json_encode($photoRequest),
          $e->getMessage()
        );
        $logRequestId = $logRequestRepository->log($logRequest);
        return $this->respondWithData([ 'res' => false, "message" => $e, "logRequestId" => $logRequestId], 400);
      }
    }
}
