<?php

/* If you want to change or add any new config, do in this file */

namespace Config;

use CodeIgniter\Config\BaseConfig;

class UserConfig extends BaseConfig {

    public $app_settings_array = array(
        "check_notification_after_every" => "60", //Check notification after every 60 seconds. Recomanded: don't set this value less than 20.
        // "custom_config" => value
    );

}
