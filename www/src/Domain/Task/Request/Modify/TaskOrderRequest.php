<?php


namespace App\Domain\Task\Request\Modify;


class TaskOrderRequest
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $extId;
    /**
     * @var string
     */
    public $action;
    /**
     * @var float
     */
    public $weight;
    /**
     * @var float
     */
    public $grossWeight;
    /**
     * @var float
     */
    public $packageWeight;
    public $planArrival;
    public $planDeparture;
    public $factArrival;
    public $factDeparture;
    public $payload;
}
