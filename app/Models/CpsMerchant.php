<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpsMerchant extends Model {

    protected $table = 'cps_merchants';
    protected $primaryKey = 'merchant_id';
    public $timestamps = false;

}
