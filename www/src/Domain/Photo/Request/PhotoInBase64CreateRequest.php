<?php

declare(strict_types=1);

namespace App\Domain\Photo\Request;


class PhotoInBase64CreateRequest
{
    public $vehicleId;

    public $ordersId;

    public $locationId;

    public $alarmsId;

    public $photo;
}
