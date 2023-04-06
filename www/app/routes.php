<?php
declare(strict_types=1);

use App\Application\Actions\Chat\ListAllChatsAction;
use App\Application\Actions\Chat\ListAllUsersAction;
use App\Application\Actions\Chat\ListChatMainStateAction;
use App\Application\Actions\Chat\ListUserMessagesAction;
use App\Application\Actions\Chat\ModifyChatMainStateAction;
use App\Application\Actions\Chat\ResetChatCounterAction;
use App\Application\Actions\Chat\SaveUserMessageAction;
use App\Application\Actions\Contractor\DelContractorAction;
use App\Application\Actions\Contractor\ListContractorAction;
use App\Application\Actions\Contractor\SaveContractorAction;
use App\Application\Actions\Contractor\ViewContractorAction;
use App\Application\Actions\Device\DelDeviceAction;
use App\Application\Actions\Device\ListDeviceAction;
use App\Application\Actions\Device\SaveDeviceAction;
use App\Application\Actions\Device\ViewDeviceAction;
use App\Application\Actions\Geo\DelGeoAction;
use App\Application\Actions\Geo\ListGeoAction;
use App\Application\Actions\Geo\SaveGeoAction;
use App\Application\Actions\Geo\ViewGeoAction;
use App\Application\Actions\Settings\DefaultGroupHandleAction;
use App\Application\Actions\Settings\DefaultUserHandleAction;
use App\Application\Actions\Settings\ListSettingsGroupAction;
use App\Application\Actions\Settings\ListSettingsUserAction;
use App\Application\Actions\Settings\SaveGroupSettingsAction;
use App\Application\Actions\Settings\SaveUserSettingsAction;
use App\Application\Actions\User\Group\DelGroupAction;
use App\Application\Actions\User\Rule\DelRulesAction;
use App\Application\Actions\Settings\ListSettingsAction;
use App\Application\Actions\Settings\SaveDefaultSettingsAction;
use App\Application\Actions\Task\ListTaskAction;
use App\Application\Actions\Task\TaskOrder\ListTaskModalAction;
use App\Application\Actions\User\AuthUserAction;
use App\Application\Actions\User\DelUserAction;
use App\Application\Actions\User\Group\ListGroupAction;
use App\Application\Actions\User\Group\SaveGroupAction;
use App\Application\Actions\User\Group\ViewGroupAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\LogoutUserAction;
use App\Application\Actions\User\Rule\ListRulesAction;
use App\Application\Actions\User\Rule\SaveRulesAction;
use App\Application\Actions\User\Rule\ViewRulesAction;
use App\Application\Actions\User\SaveUserAction;
use App\Application\Actions\User\ViewCurrentUserAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Vehicle\DelVehicleAction;
use App\Application\Actions\Vehicle\ListVehicleAction;
use App\Application\Actions\Vehicle\SaveVehicleAction;
use App\Application\Actions\Vehicle\Types\DelVehiclesTypesAction;
use App\Application\Actions\Vehicle\Types\ListVehiclesTypesAction;
use App\Application\Actions\Vehicle\Types\SaveVehiclesTypesAction;
use App\Application\Actions\Vehicle\Types\ViewVehiclesTypesAction;
use App\Application\Actions\Vehicle\Containers\DelVehiclesContainersAction;
use App\Application\Actions\Vehicle\Containers\ListVehiclesContainersAction;
use App\Application\Actions\Vehicle\Containers\SaveVehiclesContainersAction;
use App\Application\Actions\Vehicle\Containers\ViewVehiclesContainersAction;
use App\Application\Actions\Vehicle\ViewVehicleAction;
use App\Application\Middleware\SecureRouteMiddleware;
use App\Application\Middleware\SetRoles;
use App\Application\Providers\RoleProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Settings\SettingsInterface;
use Slim\Views\PhpRenderer;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($app) {
        $settings = $this->get(SettingsInterface::class);
        $path = $settings->get('templates');
        $renderer = new PhpRenderer($path);
        $args = [];

        return $renderer->render($response, "index.phtml", $args);
    });

    $app->map(['POST'], '/chat-mock', function ($request, Response $response, $args) {

        return $response;
    });

    $app->group('/settings', function (Group $group) {
        $group->group('/default', function (Group $group) {
            $group->get('', ListSettingsAction::class);
            $group->post('/save', SaveDefaultSettingsAction::class);
        })->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
          ->add(new SetRoles(['admin']));

        $group->group('/user', function (Group $group) {
            $group->get('/{id}', ListSettingsUserAction::class);
            $group->post('/{id}/save', SaveUserSettingsAction::class);
            $group->post('/{id}/default/{handle}', DefaultUserHandleAction::class);
        });

        $group->group('/group', function (Group $group) {
            $group->get('/{id}', ListSettingsGroupAction::class);
            $group->post('/{id}/save', SaveGroupSettingsAction::class);
            $group->post('/{id}/default/{handle}', DefaultGroupHandleAction::class);
        })->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
          ->add(new SetRoles(['admin']));
    });

    $app->group('/auth', function (Group $group) {
        $group->get('/info', ViewCurrentUserAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['user']));
        $group->post('/login', AuthUserAction::class);
        $group->post('/logout', LogoutUserAction::class);
    });

    $app->group('/tasks', function (Group $group) {
        $group->get('', ListTaskAction::class);
        $group->get('/{id}', ListTaskModalAction::class);
    });

    $app->group('/contractors', function (Group $group) {
        $group->get('', ListContractorAction::class);
        $group->post('/{id}', SaveContractorAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->get('/{id}', ViewContractorAction::class);
        $group->delete('/{id}', DelContractorAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
    });

    $app->group('/devices', function (Group $group) {
        $group->get('', ListDeviceAction::class);
        $group->get('/short', ListDeviceAction::class);
        $group->post('/{id}', SaveDeviceAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->get('/{id}', ViewDeviceAction::class);
        $group->delete('/{id}', DelDeviceAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
    });

    $app->group('/vehicles', function (Group $group) {
        $group->group('/types', function (Group $group) {
          $group->get('', ListVehiclesTypesAction::class);
          $group->get('/{id}', ViewVehiclesTypesAction::class);
          $group->post('/{id}', SaveVehiclesTypesAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin', 'logist']));
          $group->delete('/{id}', DelVehiclesTypesAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin', 'logist']));
        });

        $group->group('/containers', function (Group $group) {
          $group->get('', ListVehiclesContainersAction::class);
          $group->get('/{id}', ViewVehiclesContainersAction::class);
          $group->post('/{id}', SaveVehiclesContainersAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin', 'logist']));
          $group->delete('/{id}', DelVehiclesContainersAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin', 'logist']));
        });

        $group->get('', ListVehicleAction::class);
        $group->post('/{id}', SaveVehicleAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->get('/short', ListVehicleAction::class);
        $group->get('/{id}', ViewVehicleAction::class);
        $group->delete('/{id}', DelVehicleAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
    });


    $app->group('/geoobjects', function (Group $group) {
        $group->get('', ListGeoAction::class);
        $group->get('/short', ListGeoAction::class);
        $group->post('/{id}', SaveGeoAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->get('/{id}', ViewGeoAction::class);
        $group->delete('/{id}', DelGeoAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
    });

    $app->group('/users', function (Group $group) {
        $group->group('/rules', function (Group $group) {
            $group->get('', ListRulesAction::class);
            $group->post('/{id}', SaveRulesAction::class)
              ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
              ->add(new SetRoles(['admin']));
            $group->get('/{id}', ViewRulesAction::class);
            $group->delete('/{id}', DelRulesAction::class)
              ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
              ->add(new SetRoles(['admin']));
        });

        $group->group('/groups', function (Group $group) {
            $group->get('', ListGroupAction::class);
            $group->post('/{id}', SaveGroupAction::class)
              ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
              ->add(new SetRoles(['admin']));
            $group->get('/{id}', ViewGroupAction::class);
            $group->delete('/{id}', DelGroupAction::class)
              ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
              ->add(new SetRoles(['admin']));
        });

        $group->get('', ListUsersAction::class);
        $group->get('/short', ListUsersAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->post('/{id}', SaveUserAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
        $group->get('/{id}', ViewUserAction::class);
        $group->delete('/{id}', DelUserAction::class)
            ->add(new SecureRouteMiddleware($group->getContainer()->get(RoleProvider::class)))
            ->add(new SetRoles(['admin']));
    });

    $app->group('/chat', function (Group $group) {
        $group->get('', ListChatMainStateAction::class);
        $group->post('', ModifyChatMainStateAction::class);
        $group->get('/users', ListAllUsersAction::class);
        $group->get('/chats', ListAllChatsAction::class);
        $group->get('/users/{id}/messages', ListUserMessagesAction::class);
        $group->post('/users/{id}/messages', SaveUserMessageAction::class);
        $group->post('/users/{id}/counter', ResetChatCounterAction::class);
    });
};
