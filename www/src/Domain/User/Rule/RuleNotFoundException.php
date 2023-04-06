<?php
declare(strict_types=1);

namespace App\Domain\User\Rule;

use App\Domain\DomainException\DomainRecordNotFoundException;

class RuleNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The rule you requested does not exist.';
}
