<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpsClick extends Model {

    protected $table = 'cps_clicks';
    protected $primaryKey = 'click_id';
    public $timestamps = false;

}
