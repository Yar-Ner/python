<?php
declare(strict_types=1);

namespace App\Domain\User\Rule\Request;

class UpdateRuleRequest
{

    /**
     * todo: Assert here
     * @var string
     */
    public $name;

    /** @var string|null */
    public $description;

    /** @var string */
    public $handle;

    public static function createFromArray(array $data): UpdateRuleRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->handle = $data['handle'];
        $obj->description = $data['description'];

        return $obj;

    }

}