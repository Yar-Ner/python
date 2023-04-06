<?php

declare(strict_types=1);

namespace App\Domain\SharedContext;


use App\Domain\Photo\Request\PhotoInBase64CreateRequest;

class FileUploaderService
{
    private const BASE64_TYPE = '/^data:[\w\W]*?\/(\w+)[\w\W]*?;base64,/';
    private const MIME_TYPE_POSITION = 1;
    public const DIR_FOR_FILES = 'public/';
    
    private $uploadFilePrefix;

    public function __construct(string $uploadFilePrefix)
    {
        $this->uploadFilePrefix = $uploadFilePrefix;
    }

    public function uploadFileFromBase64(PhotoInBase64CreateRequest $fileRequest): File
    {
        $fileName = $this->generateNameWithExtension($this->getMimeType($fileRequest->photo));

        $this->createRealFile(
            $fileName,
            \base64_decode(\preg_replace(self::BASE64_TYPE, '', $fileRequest->photo))
        );

        return new File(
            \sprintf('/files/%s', $fileName),
            $fileName
        );
    }

    public function moveFile($file, $params)
    {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $name = $file["name"];
            $fileInfo = pathinfo($name);

            switch (strtolower($fileInfo['extension'])) {
                case 'php':
                case 'exe':
                case 'cgi':
                case 'js':
                case 'html':
                    return [ 'status' => "error"];
                default:
                    break;
            }

            $randomFileName = $this->getRandomFileName($name, $params['userId']);
            $fullFileName = $randomFileName . "." . $fileInfo['extension'];

            if (!move_uploaded_file($file["tmp_name"], "$this->uploadFilePrefix/$fullFileName")) {
                return [ 'status' => "error"];
            }

            $data = [
                'name' => $name,
                'time' => Date('y-m-d h:i:s'),
                'user' => $params['userId'],
                'size' => filesize("$this->uploadFilePrefix/$fullFileName"),
                'hash' => $fullFileName
            ];

            if (isset($params['recipientId'])) $data['recipientId'] = $params['recipientId'];

            file_put_contents($this->uploadFilePrefix . '/' . $randomFileName . '.info.txt', json_encode($data));

            return ['filename' => $name, 'hash' => $fullFileName, 'infoPath' => $randomFileName . '.info.txt'];
        } else {
            return [ 'status' => "error"];
        }
    }

    public function createRealFile(string $name, string $content): void
    {
        file_put_contents(\sprintf('%s/%s', $this->uploadFilePrefix, $name), $content);
    }

    private function generateNameWithExtension(string $extension): string
    {
        return \sprintf('%s.%s', \uniqid('', true), $extension);
    }

    private function getMimeType(string $fileBase64): string
    {
        \preg_match(self::BASE64_TYPE, $fileBase64, $mimeType);

        return $mimeType[self::MIME_TYPE_POSITION];
    }

    public static function getRandomFileName($sourceFile, $userId) {
        return Date("ymdhis") . rand();//$userId;
    }

}