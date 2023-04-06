<?php


namespace App\Domain\Task\Request\Modify;


class TaskAddressRequest
{

    /**
     * @var AddressRequest
     */
    public $id;
    public $extId;
    public $address;
    public $lat;
    public $long;
    public $radius;
}
