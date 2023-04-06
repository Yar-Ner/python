<?php
declare(strict_types=1);

namespace App\Domain\Task;

class Task implements \JsonSerializable
{
    private $id;

    private $user_id;

    private $driverName;

    private $vehicles_id;

    private $ext_id;

    private $number;

    private $status;

    private $loaded_weight;

    private $empty_weight;

    private $comment;

    private $starttime;

    private $endtime;

    private $updated;

    private $distance;

    private $addresses;

    public function __construct(
      ?int $id,
      ?int $user_id,
      ?string $driverName,
      int $vehicles_id,
      string $ext_id,
      string $number,
      string $status,
      ?float $loaded_weight,
      ?float $empty_weight,
      ?string $comment,
      ?string $starttime,
      ?string $endtime,
      ?string $updated,
      ?int $distance,
      ?array $addresses
    )
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->driverName = $driverName;
        $this->vehicles_id = $vehicles_id;
        $this->ext_id = $ext_id;
        $this->number = $number;
        $this->status = $status;
        $this->loaded_weight = $loaded_weight;
        $this->empty_weight = $empty_weight;
        $this->comment = $comment;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
        $this->updated = $updated;
        $this->distance = $distance;
        $this->addresses = $addresses;
    }

    public function jsonSerialize(): array
    {
        $return = [
            'id' => $this->id,
            'vehicles_id' => $this->vehicles_id,
            'ext_id' => $this->ext_id,
            'number' => $this->number,
            'status' => $this->status,
            'comment' => $this->comment,
            'addresses' => $this->addresses
        ];
        if ($this->user_id) $return['user_id'] = $this->user_id;
        if ($this->starttime) $return['starttime'] = $this->starttime;
        if ($this->endtime) $return['endtime'] = $this->endtime;
        if ($this->updated) $return['updated'] = $this->updated;
        if ($this->loaded_weight) $return['loaded_weight'] = $this->loaded_weight;
        if ($this->empty_weight) $return['empty_weight'] = $this->empty_weight;
        if ($this->distance) $return['distance'] = $this->distance;

        return $return;
    }

    private function latlng2distance($lat1, $long1, $lat2, $long2) {
      //радиус Земли
      $R = 6372795;
      //перевод коордитат в радианы
      $lat1 *= pi() / 180;
      $lat2 *= pi() / 180;
      $long1 *= pi() / 180;
      $long2 *= pi() / 180;
      //вычисление косинусов и синусов широт и разницы долгот
      $cl1 = cos($lat1);
      $cl2 = cos($lat2);
      $sl1 = sin($lat1);
      $sl2 = sin($lat2);
      $delta = $long2 - $long1;
      $cdelta = cos($delta);
      $sdelta = sin($delta);
      //вычисления длины большого круга
      $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
      $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
      $ad = atan2($y, $x);
      $dist = $ad * $R;
      //расстояние между двумя координатами в метрах
      return $dist;
    }

    public function getDistanceByTrackPoints($trackPoints)  {
      $distance = 0;
      for ($i = 0; $i < count($trackPoints) - 1; $i++) {
        $res = $this->latlng2distance(
          $trackPoints[$i]->getLatitude(),
          $trackPoints[$i]->getLongitude(),
          $trackPoints[$i + 1]->getLatitude(),
          $trackPoints[$i + 1]->getLongitude()
        );
        $distance += $res;
      }
      return $distance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDriverName(): ?string
    {
      return $this->driverName;
    }

    public function getStarttime(): ?string
    {
      return $this->starttime;
    }

    public function getEndtime(): ?string
    {
      return $this->endtime;
    }

    public function getVehicleId(): int
    {
        return $this->vehicles_id;
    }

    public function getExtId(): string
    {
        return $this->ext_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNumber(): string
    {
      return $this->number;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }
}