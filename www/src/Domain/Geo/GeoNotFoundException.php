<?php
declare(strict_types=1);

namespace App\Domain\Geo;

use App\Domain\DomainException\DomainRecordNotFoundException;

class GeoNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The geoobject you requested does not exist.';
}
