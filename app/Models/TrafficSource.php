<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficSource extends Model {

    protected $table = 'traffic_sources';
    protected $primaryKey = 'source_id';
    public $timestamps = false;

}
