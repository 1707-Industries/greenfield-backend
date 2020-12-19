<?php

namespace App;

class AppInfo
{

    public function getInfo()
    {
        return [
            'app_name' => config('app.name'),
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];
    }
}
