<?php
declare(strict_types=1);

use App\Application\Actions\Alarm\ListAlarmsAction;
use App\Application\Actions\Alarm\ListAlarmsVehiclesAction;
use App\Application\Actions\Alarm\ChangeActiveVehicleAlarmAction;
use App\Application\Actions\Alarm\CreateVehicleAlarmAction;
use App\Application\Actions\Device\ListDeviceAction;
use App\Application\Actions\Geo\ListGeoAction;
use App\Application\Actions\Photo\CreatePhotoAction;
use App\Application\Actions\Photo\ListPhotoAction;
use App\Application\Actions\User\Device\ListUsersDevicesAction;
use App\Application\Actions\User\Rule\ListUserRules;
use App\Application\Actions\User\Settings\ListUserSettings;
use App\Application\Actions\User\Vehicle\ListUserVehicles;
use App\Application\Actions\Vehicle\ListVehicleAction;
use App\Application\Actions\Monitoring\ListMonitoringAction;
use App\Application\Actions\Monitoring\ViewMonitoringAction;
use App\Application\Actions\Monitoring\SaveMonitoringAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app) {
    $app->group('/api', function (Group $group) {
        $group->group('/user', function (Group $group) {
            $group->get('/config', ListUserSettings::class);
            $group->get('/rules', ListUserRules::class);
            $group->get('/vehicles', ListUserVehicles::class);
            $group->get('/devices', ListUsersDevicesAction::class);
        });

        $group->group('/geo', function (Group $group) {
            $group->get('/objects', ListGeoAction::class);
        });

        $group->group('/alarm', function (Group $group) {
            $group->get('s', ListAlarmsAction::class);
            $group->get('s/vehicles', ListAlarmsVehiclesAction::class);
            $group->post('', CreateVehicleAlarmAction::class);
            $group->post('/read/{id}', ChangeActiveVehicleAlarmAction::class);
        });

        $group->group('/tasks', function (Group $group) {
            $group->post('/{id}/start', \App\Application\Actions\Task\StartTaskAction::class);
            $group->post('/{id}/finish', \App\Application\Actions\Task\FinishTaskAction::class);
            $group->post('/{id}/pause', \App\Application\Actions\Task\PauseTaskAction::class);
            $group->post('/{id}/arrive', \App\Application\Actions\Task\ArriveTaskAction::class);
            $group->post('/{id}/leave', \App\Application\Actions\Task\LeaveTaskAction::class);
            $group->post('/{id}/modify', \App\Application\Actions\Task\ModifyTaskAction::class);
            $group->post('/{id}/assign', \App\Application\Actions\Task\AssignAction::class);
            $group->post('/{id}/status/{status}', \App\Application\Actions\Task\StatusTaskAction::class);
            $group->post('/{id}/weight', \App\Application\Actions\Task\WeightTaskAction::class);
            $group->post('/{id}/payload', \App\Application\Actions\Task\PayloadTaskAction::class);
        });

        $group->post('/photo', CreatePhotoAction::class);
        $group->get('/photos', ListPhotoAction::class);
        $group->get('/devices', ListDeviceAction::class);
        $group->get('/vehicles', ListVehicleAction::class);

        $group->group('/messages', function (Group $group) {
            $group->get('/get', \App\Application\Actions\User\Message\ListAction::class);
            $group->post('/delivered', \App\Application\Actions\User\Message\MarkAsDeliveredAction::class);
            $group->post('/read', \App\Application\Actions\User\Message\MarkAsReadAction::class);
            $group->post('/send', \App\Application\Actions\User\Message\SendAction::class);
        });

        $group->group('/monitoring', function (Group $group) {
          $group->get('',  ListMonitoringAction::class);
          $group->post('/location', SaveMonitoringAction::class);
          $group->get('/locations/{id}', ViewMonitoringAction::class);
        });

    });
};