<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronjobController;

class CronjobDaily extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $cronjob = new CronjobController;
        $cronjob->daily();
    }

}
