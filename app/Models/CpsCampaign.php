<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpsCampaign extends Model {

    protected $table = 'cps_campaigns';
    protected $primaryKey = 'campaign_id';
    public $timestamps = false;

}
