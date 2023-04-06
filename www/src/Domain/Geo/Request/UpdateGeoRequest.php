<?php
declare(strict_types=1);

namespace App\Domain\Geo\Request;

class UpdateGeoRequest
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $address;

    /**
     * @var float
     */
    public $lat;

    /**
     * @var float
     */
    public $long;

    /**
     * @var float
     */
    public $radius;

    /**
     * @var int|null
     */
    public $deleted;

    /**
     * @var int|null
     */
    public $ext_id;

    public static function createFromArray(array $data): UpdateGeoRequest
    {
        $obj = new self();

        $obj->ext_id = $data['ext_id'];
        if (isset($data['name'])) $obj->name = $data['name'];
        if (isset($data['type'])) $obj->type = $data['type'];
        if (isset($data['address'])) $obj->address = $data['address'];
        if (isset($data['lat'])) $obj->lat = $data['lat'];
        if (isset($data['long'])) $obj->long = $data['long'];
        if (isset($data['radius'])) $obj->radius = $data['radius'];
        $obj->deleted = $data['deleted'] ?? 0;

        return $obj;
    }
}
