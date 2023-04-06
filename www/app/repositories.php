<?php

declare(strict_types=1);

use App\Domain\Alarm\AlarmRepository;
use App\Domain\Chat\ChatMessageRepository;
use App\Domain\Contractor\ContractorRepository;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\Settings\DefaultSettingsRepository;
use App\Domain\SharedContext\Log\LogRequestRepository;
use App\Domain\Task\TaskAddress\TaskAddressRepository;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskOrder\TaskOrderRepository;
use App\Domain\User\Group\GroupRepository;
use App\Domain\User\Message\MessageRepositoryInterface;
use App\Domain\User\Rule\RuleRepository;
use App\Domain\User\UserRepository;
use App\Domain\Device\DeviceRepository;
use App\Domain\Geo\GeoRepository;
use App\Domain\User\UserTokenRepository;
use App\Domain\Vehicle\Types\VehiclesTypesRepository;
use App\Domain\Vehicle\Containers\VehiclesContainersRepository;
use App\Domain\Vehicle\VehicleRepository;
use App\Domain\Monitoring\MonitoringRepository;
use App\Infrastructure\Persistence\Alarm\MySQLAlarmRepository;
use App\Infrastructure\Persistence\Chat\MySQLChatMessageRepository;
use App\Infrastructure\Persistence\Contractor\MySQLContractorRepository;
use App\Infrastructure\Persistence\Device\MySQLDeviceRepository;
use App\Infrastructure\Persistence\Geo\MySQLGeoRepository;
use App\Infrastructure\Persistence\Log\MySQLLogRequestRepository;
use App\Infrastructure\Persistence\Photo\MySQLPhotoRepository;
use App\Infrastructure\Persistence\Settings\MySQLDefaultSettingsRepository;
use App\Infrastructure\Persistence\Task\MySQLTaskRepository;
use App\Infrastructure\Persistence\Task\TaskAddress\MySQLTaskAddressRepository;
use App\Infrastructure\Persistence\Task\TaskOrder\MySQLTaskOrderRepository;
use App\Infrastructure\Persistence\User\Group\MySQLGroupRepository;
use App\Infrastructure\Persistence\User\Message\MySQLMessageRepositoryRepository;
use App\Infrastructure\Persistence\User\MySQLUserRepository;
use App\Infrastructure\Persistence\User\MySQLUserTokenRepository;
use App\Infrastructure\Persistence\User\Rule\MySQLRuleRepository;
use App\Infrastructure\Persistence\Vehicle\MySQLVehiclesRepository;
use App\Infrastructure\Persistence\Monitoring\MySQLMonitoringRepository;
use App\Infrastructure\Persistence\Vehicle\Types\MySQLVehiclesTypesRepository;
use App\Infrastructure\Persistence\Vehicle\Containers\MySQLVehiclesContainersRepository;
use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            GeoRepository::class => autowire(MySQLGeoRepository::class),
            RuleRepository::class => autowire(MySQLRuleRepository::class),
            UserRepository::class => autowire(MySQLUserRepository::class),
            GroupRepository::class => autowire(MySQLGroupRepository::class),
            ContractorRepository::class => autowire(MySQLContractorRepository::class),
            TaskRepository::class => autowire(MySQLTaskRepository::class),
            TaskOrderRepository::class => autowire(MySQLTaskOrderRepository::class),
            DeviceRepository::class => autowire(MySQLDeviceRepository::class),
            UserTokenRepository::class => autowire(MySQLUserTokenRepository::class),
            VehicleRepository::class => autowire(MySQLVehiclesRepository::class),
            VehiclesTypesRepository::class => autowire(MySQLVehiclesTypesRepository::class),
            VehiclesContainersRepository::class => autowire(MySQLVehiclesContainersRepository::class),
            DefaultSettingsRepository::class => autowire(MySQLDefaultSettingsRepository::class),
            TaskAddressRepository::class => autowire(MySQLTaskAddressRepository::class),
            PhotoRepositoryInterface::class => autowire(MySQLPhotoRepository::class),
            MonitoringRepository::class => autowire(MySQLMonitoringRepository::class),
            AlarmRepository::class => autowire(MySQLAlarmRepository::class),
            LogRequestRepository::class => autowire(MySQLLogRequestRepository::class),
            MessageRepositoryInterface::class => autowire(MySQLMessageRepositoryRepository::class),
            ChatMessageRepository::class => autowire(MySQLChatMessageRepository::class),
        ]
    );
};