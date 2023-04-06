<?php


namespace App\Domain\Task\Request\Modify;


class AddressRequest
{
    public $id;
    public $extId;
    public $address;
    public $lat;
    public $long;
    public $radius;
    /**
     * @var AddressContractorRequest
     */
    public $contactor;

}
