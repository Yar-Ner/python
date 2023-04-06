<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Settings;

use App\Domain\Settings\DefaultSettings;
use App\Domain\Settings\DefaultSettingsRepository;
use App\Domain\Settings\GroupSettings;
use App\Domain\Settings\UserSettings;

class MySQLDefaultSettingsRepository implements DefaultSettingsRepository
{
    /**
     * @var \PDO[]
     */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = 'SELECT handle, val, updated FROM cfg_setting';
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = new DefaultSettings($row['handle'], $row['val'], new \DateTime($row['updated']));
        }

        return $result;
    }

    public function findAllUser(int $id): array
    {
        $settings = $this->findAll();
        $result = [];
        $user_groups = $this->findGroupsIdByUserId($id);
        if (count($settings)) {
            foreach ($settings as $setting) {
                $row = [];
                $row['handle'] = $setting->getHandle();
                $row['main'] = false;
                $userSetting = $this->getUserSettingByHandle($setting->getHandle(), $id);
                if ($userSetting) {
                    $row['main'] = true;
                    $row['val'] = $userSetting->getVal();
                } else {
                    foreach ($user_groups as $group_id) {
                        if (isset($row['val'])) {
                            break;
                        } else {
                            $groupSetting = $this->getGroupSettingByHandle($setting->getHandle(), $group_id);
                            if ($groupSetting) $row['val'] = $groupSetting->getVal();
                        }
                    }
                    if (!isset($row['val'])) {
                        $row['val'] = $setting->getVal();
                    }
                }
                $result[] = $row;
            }
            usort ($result, function ($left, $right) {
                return $right['main'] - $left['main'];
            });

            return $result;
        }
    }

    public function findAllGroup(int $id): array
    {
        $settings = $this->findAll();
        $result = [];
        if (count($settings)) {
            foreach ($settings as $setting) {
                $row = [];
                $row['handle'] = $setting->getHandle();
                $row['main'] = false;
                $groupSetting = $this->getGroupSettingByHandle($setting->getHandle(), $id);
                if ($groupSetting) {
                    $row['main'] = true;
                    $row['val'] = $groupSetting->getVal();
                } else {
                    $row['val'] = $setting->getVal();
                }

                $result[] = $row;
            }
        }

        usort ($result, function ($left, $right) {
            return $right['main'] - $left['main'];
        });

        return $result;
    }

    public function save(DefaultSettings $settings, string $type): void
    {
        if (in_array($type, ['user', 'group'])) {
            $table = 'cfg_user_setting';
            $col = 'acl_user_id';
            if ($type === 'group') {
                $table = 'cfg_group_setting';
                $col = 'acl_user_group_id';
            }
            $sql = "DELETE FROM $table where handle = '" . $settings->getHandle() . "' 
            AND $col = ";
            $sql .= $type === 'group' ? $settings->getGroupId() : $settings->getUserId();
            $this->connection->exec($sql);

            $sql = "INSERT INTO $table SET handle = '" . $settings->getHandle() . "', val = '" . $settings->getVal() . "',
            updated = NOW()";

            $sql .= $type === 'group'
                ? ", acl_user_group_id = " . $settings->getGroupId()
                : ", acl_user_id = " . $settings->getUserId();

            $this->connection->exec($sql);
        } else {
            $sql = "DELETE FROM cfg_setting where handle = '" . $settings->getHandle() . "'";

            $this->connection->exec($sql);
            $sql = "INSERT INTO cfg_setting SET handle = '" . $settings->getHandle() . "', val = '" . $settings->getVal() . "', 
             updated = NOW()";

            $this->connection->exec($sql);
        }
    }

    public function defaultUserHandle(int $id, string $handle):void
    {
      $this->connection->exec("DELETE FROM cfg_user_setting WHERE acl_user_id=$id AND handle='$handle'");
    }

    public function defaultGroupHandle(int $id, string $handle):void
    {
      $this->connection->exec("DELETE FROM cfg_group_setting WHERE acl_user_group_id=$id AND handle='$handle'");
    }

    public function findOneByHandle(string $handle): ?DefaultSettings
    {
        $sql = "SELECT handle, val FROM cfg_setting WHERE handle = '" . trim(addslashes($handle)) . "' LIMIT 1";
        $res = $this->connection->query($sql);
        $row = $res->fetch();

        if ($row) {
            return new DefaultSettings($row['handle'], $row['val']);
        }

        return null;
    }

    public function getGroupSettingByHandle($handle, $group_id) {
        $sql = "SELECT id, handle, val FROM cfg_group_setting WHERE handle = '".trim(addslashes($handle))."' AND acl_user_group_id = '".intval($group_id)."' LIMIT 1";
        $res = $this->connection->query($sql);
        $row = $res->fetch();
        if ($row) {
            return new GroupSettings($row['handle'], $row['val']);
        }
        return null;
    }

    public function getUserSettingByHandle($handle, $user_id) {
        $sql = "SELECT id, handle, val FROM cfg_user_setting WHERE handle = '".trim(addslashes($handle))."' AND acl_user_id = '".intval($user_id)."' LIMIT 1";
        $res = $this->connection->query($sql);
        $row = $res->fetch();
        if ($row) {
            return new UserSettings($row['handle'], $row['val']);
        }
        return null;
    }

    private function findGroupsIdByUserId(int $id) {
        $sql = 'SELECT acl_user_group_id FROM acl_user_group_has_users WHERE acl_user_id=' . $id;
        $result = [];
        $res = $this->connection->query($sql);

        while ($row = $res->fetch()) {
            $result[] = (int)$row['acl_user_group_id'];
        }

        return $result;
    }
}
