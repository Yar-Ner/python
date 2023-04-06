<?php
declare(strict_types=1);

namespace App\Domain\Task\TaskOrder;

class TaskOrder implements \JsonSerializable
{
    private $id;

    private $task_addresses_id;

    private $ext_id;

    private $action;

    private $volume;

    private $weight;

    private $gross_weight;

    private $package_weight;

    private $status;

    private $failed_reason;

    private $plan_arrival;

    private $plan_departure;

    private $fact_arrival;

    private $fact_departure;

    private $payload;

    private $comment;

    public function __construct(
        ?int $id,
        int $task_addresses_id,
        string $ext_id,
        string $action,
        ?float $volume,
        ?float $weight,
        ?float $gross_weight,
        ?float $package_weight,
        ?string $status,
        ?string $failed_reason,
        ?string $plan_arrival,
        ?string $plan_departure,
        ?string $fact_arrival,
        ?string $fact_departure,
        ?string $payload,
        ?string $comment
    )
    {
        $this->id = $id;
        $this->task_addresses_id = $task_addresses_id;
        $this->ext_id = $ext_id;
        $this->action = $action;
        $this->volume = $volume;
        $this->weight = $weight;
        $this->gross_weight = $gross_weight;
        $this->package_weight = $package_weight;
        $this->status = $status;
        $this->failed_reason = $failed_reason;
        $this->plan_arrival = $plan_arrival;
        $this->plan_departure = $plan_departure;
        $this->fact_arrival = $fact_arrival;
        $this->fact_departure = $fact_departure;
        $this->payload = $payload;
        $this->comment = $comment;

    }

    public function jsonSerialize(): array
    {
        $return = [
            'id' => $this->id,
            'task_addresses_id' => $this->task_addresses_id,
            'ext_id' => $this->ext_id,
            'action' => $this->action,
            'weight' => $this->weight,
            'gross_weight' => $this->gross_weight,
            'package_weight' => $this->package_weight,
            'status' => $this->status,
        ];

        if ($this->plan_arrival) $return['plan_arrival'] = $this->plan_arrival;
        if ($this->plan_departure) $return['plan_departure'] = $this->plan_departure;
        if ($this->fact_arrival) $return['fact_arrival'] = $this->fact_arrival;
        if ($this->fact_departure) $return['fact_departure'] = $this->fact_departure;
        if ($this->volume) $return['volume'] = $this->volume;
        if ($this->failed_reason) $return['failed_reason'] = $this->failed_reason;
        if ($this->payload) $return['payload'] = json_decode($this->payload);
        if ($this->comment) $return['comment'] = $this->comment;

        return $return;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskAddressId(): int
    {
        return $this->task_addresses_id;
    }

    public function getExtId(): string
    {
        return $this->ext_id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getVolume(): ?float
    {
      return $this->volume;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getGrossWeight(): float
    {
        return $this->gross_weight;
    }

    public function getPackageWeight(): float
    {
        return $this->package_weight;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getFailedReason(): string
    {
        return $this->failed_reason;
    }

    public function getPlanArrival(): string
    {
        return $this->plan_arrival;
    }

    public function getFactArrival(): string
    {
        return $this->fact_arrival;
    }

    public function getFactDeparture(): string
    {
        return $this->fact_departure;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}