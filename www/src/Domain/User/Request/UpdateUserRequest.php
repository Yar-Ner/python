<?php
declare(strict_types=1);

namespace App\Domain\User\Request;


class UpdateUserRequest
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $fullname;

    /**
     * @var array
     */
    public $groupsId;

    /**
     * @var array
     */
    public $rulesId;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string|null
     */
    public $description;

    public static function createFromArray(array $data): UpdateUserRequest
    {
        $obj = new self();

        $obj->username = $data['username'];
        $obj->password = $data['password'];
        $obj->fullname = $data['fullname'];
        $obj->status = $data['status'];
        if (isset($data['rulesId'])) $obj->rulesId = $data['rulesId'] === '' ? [] : explode(',', $data['rulesId']);
        if (isset($data['groupsId'])) $obj->groupsId = $data['groupsId'] === '' ? [] : explode(',', $data['groupsId']);
        if (isset($data['description'])) $obj->description = $data['description'];

        return $obj;
    }
}
