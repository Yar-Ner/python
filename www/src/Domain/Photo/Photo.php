<?php

declare(strict_types=1);

namespace App\Domain\Photo;


use App\Domain\Photo\Request\PhotoInBase64CreateRequest;
use App\Domain\SharedContext\File;

class Photo
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var int|null
     */
    private $vehiclesId;
    /**
     * @var int|null
     */
    private $ordersId;
    /**
     * @var int|null
     */
    private $alarmId;
    /**
     * @var int|null
     */
    private $locationId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $uploaded;

    public function __construct(
        ?int $id,
        string $path,
        string $name,
        int $userId,
        ?int $vehiclesId,
        ?int $ordersId,
        ?int $alarmId,
        ?int $locationId,
        ?string $uploaded = null
    ) {
        $this->path = $path;
        $this->userId = $userId;
        $this->vehiclesId = $vehiclesId;
        $this->ordersId = $ordersId;
        $this->alarmId = $alarmId;
        $this->locationId = $locationId;
        $this->name = $name;
        $this->id = $id;
        $this->uploaded = $uploaded;
    }

    public static function makeByRequest(File $file, int $creatorId, PhotoInBase64CreateRequest $request): self
    {
        return new self(
            null,
            $file->getFilePath(),
            $file->getRealName(),
            $creatorId,
            (int)$request->vehicleId,
            (int)$request->ordersId,
            (int)$request->alarmsId,
            (int)$request->locationId
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }


    public function getVehiclesId(): ?int
    {
        return $this->vehiclesId;
    }

    public function getOrderId(): ?int
    {
        return $this->ordersId;
    }

    public function getAlarmId(): ?int
    {
        return $this->alarmId;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploaded(): ?string
    {
        return $this->uploaded;
    }

}
