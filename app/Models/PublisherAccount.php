<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherAccount extends Model {

    protected $table = 'publisher_accounts';
    protected $primaryKey = 'account_id';
    public $timestamps = false;

}
