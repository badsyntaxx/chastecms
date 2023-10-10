<?php 

/**
 * Settings Model Class
 *
 * Interact with the database to process data related to the settings.
 */
class SettingsModel extends Model
{
    public function getSetting($data)
    {
        // SELECT `value` FROM `settings` WHERE `name` = "sitename"
        $select = $this->table('settings')->select('value')->where('name', $data)->limit(1)->getOne();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function updateSetting($data)
    {
        // UPDATE `settings` SET `value` = ? WHERE `id` = ?
        $update = $this->table('settings')->update($data)->where('settings_id')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                return empty($update['response']) ? true : $update['response'];
            } else {
                return false;
            }
        }
    }

    public function getMailSettings()
    {
        // SELECT * FROM `mail`
        $select = $this->table('mail')->select('*')->get();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function updateMailSettings($data)
    {
        // UPDATE `mail` SET `username` = ? WHERE `id` = ?
        $update = $this->table('mail')->update($data)->where('mail_id')->execute();
        if ($update) {
            if ($update['status'] == 'success') {
                return empty($update['response']) ? true : $update['response'];
            } else {
                return false;
            }
        }
    }

    public function getLanguages()
    {
        // // SELECT * FROM `language`
        $select = $this->table('language')->select('*')->getAll();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }

    public function getAnalyticsCode()
    {
        // SELECT * FROM `analytics`
        $select = $this->table('analytics')->select('code')->limit(1)->getOne();
        if ($select) {
            if ($select['status'] == 'success') {
                return empty($select['response']) ? false : $select['response'];
            } else {
                return false;
            }
        }
    }
}