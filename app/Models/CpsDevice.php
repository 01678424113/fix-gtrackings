<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpsDevice extends Model {

    protected $table = 'cps_devices';
    protected $primaryKey = 'device_id';
    public $timestamps = false;

}
