<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Upazila;

class GenerateUpazilas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $upazilas = $this->details;
        foreach($upazilas->data as $upazila){
            $newupazila = new Upazila();
            $newupazila->zone_id = $upazila->zone_id;
            $newupazila->district_id = $district->id;
            $newupazila->title = $upazila->zone_name;
            $newupazila->save();
        }

    }
}
