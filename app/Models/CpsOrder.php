<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpsOrder extends Model {

    protected $table = 'cps_orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

}
