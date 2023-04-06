<?php


namespace App\Domain\Task\Request;


class DepartureRequest
{
    public $addressId;

    public $time;

    public static function createFromArray(array $data): self
    {
        $request = new self();

        $request->addressId = $data['address_id'];
        $request->time  = $data['time'] ?? date("Y-m-d H:i:s");

        return $request;
    }
}
