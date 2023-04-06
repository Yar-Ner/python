<?php


namespace App\Domain\Task\Request\Modify;


class ModifyRequest
{
    public $id;
    public $vehiclesId;
    public $extId;
    public $number;
    public $comment;
    public $status;
    public $loadedWeight;
    public $emptyWeight;
    public $starttime;
    public $endtime;
    public $updated;
    public $addresses;


    public function getAddresses(): ?array
    {
      return is_string($this->addresses) ? json_decode($this->addresses, true) : $this->addresses;
    }

}
