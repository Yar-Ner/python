<?php
declare(strict_types=1);

namespace App\Domain\Geo\Request;

class CreateGeoRequest
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
     * @var string|null
     */
    public $ext_id;

    public static function createFromArray(array $data): CreateGeoRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->type = $data['type'];
        $obj->address = $data['address'];
        $obj->lat = (float)$data['lat'];
        $obj->long = (float)$data['long'];
        $obj->radius = (float)$data['radius'];
        $obj->deleted = 0;
        $obj->ext_id = $data['ext_id'];

        return $obj;
    }
}
