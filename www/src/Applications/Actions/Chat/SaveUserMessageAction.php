<?php
declare(strict_types=1);

namespace App\Application\Actions\Chat;

use App\Application\Actions\Action;
use App\Domain\Chat\ChatMessage;
use App\Domain\Chat\ChatMessageRepository;
use App\Domain\Chat\Request\CreateChatMessageRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use OneSignal\Config;
use OneSignal\OneSignal;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\HttpClient\Psr18Client;

class SaveUserMessageAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        /** @var ChatMessageRepository $repository */
        $repository = $this->get(ChatMessageRepository::class);

        /** @var CreateChatMessageRequest $request */
        $request = $this->getDenormalizedRequest(CreateChatMessageRequest::class);

        $message = new ChatMessage(null, $this->user->getId(), (int) $request->recipientId, 0, $request->content, new \DateTimeImmutable("now"));
        $repository->save($message);

        $config = new Config('4c650135-21fb-471c-a5cc-b19438898286', 'Y2NiNDdiM2MtNjM3Yi00NjY1LTljYzctOTQ1NDNhNDRiNGQ5');
        $httpClient = new Psr18Client();
        $streamFactory = new Psr17Factory();
        $requestFactory = $streamFactory;
        $oneSignal = new OneSignal($config, $httpClient, $requestFactory, $streamFactory);

        $sendingResult = $oneSignal->notifications()->add([
            'contents' => [
                'en' => $message->getContent()
            ],
            'headings' => [
                'en' => $this->user->getFullname()
            ],
            'filters' => [
                [
                    'field' => 'tag',
                    'key' => 'user_id',
                    'relation' => '=',
                    'value' => $message->getRecipientId()
                ]
            ]
        ]);

        return $this->respondWithData(
            [
                'id' => $message->getId(),
                'chat_id' => $message->getRecipientId(),
                'date' => date('Y-m-d H:i:s'),
                'text' => $message->getContent(),
                'user_id' => $message->getSenderId(),
                'sending_result' => $sendingResult
            ]
        );
    }
}
