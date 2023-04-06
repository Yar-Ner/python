<?php
declare(strict_types=1);

namespace App\Domain\User\Group\Request;

class UpdateGroupRequest
{
    /**
     * todo: Assert here
     * @var string
     */
    public $name;

    /** @var string|null */
    public $description;

    /** @var array */
    public $rulesId;

    /** @var array */
    public $usersId;

    public static function createFromArray(array $data): UpdateGroupRequest
    {
        $obj = new self();

        $obj->name = $data['name'];
        $obj->rulesId = $data['rulesId'] !== '' ? explode(',', $data['rulesId']) : [];
        $obj->usersId = $data['usersId'] != '' ? explode(',', $data['usersId']) : [];
        $obj->description = $data['description'];

        return $obj;
    }
}
