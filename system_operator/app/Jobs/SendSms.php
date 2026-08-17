<?php

namespace App\Jobs;

use Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $users;
    protected $message;
    protected $getway;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, $message, $getway)
    {
        $this->users = $users;
        $this->message = $message;
        $this->getway = $getway;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // foreach ($this->users as $row) {
            if($this->getway == 'musking'){
                Helper::sendSms($this->users, $this->message);
            }else{
                Helper::sendSmsNonMusking($this->users, $this->message);
            }
        // }
    }
}
