<?php

namespace App;

class AppInfo {

    public function getInfo()
    {
        return [
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];
    }

}
