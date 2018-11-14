<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpiReport extends Model {

    protected $table = 'cpi_reports';
    protected $primaryKey = 'report_id';
    public $timestamps = false;

}
