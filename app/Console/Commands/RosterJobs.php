<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\RosterAssigneesRepository;
use App\Repositories\RosterJobsRepository;
use DB;
use Carbon\Carbon;

class RosterJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roster:job
                                {customDate? : (optional) The date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a Roster Job to a user in the company';

    private $rosterAssignees,$rosterJobs;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RosterAssigneesRepository $rosterAssignees,RosterJobsRepository $rosterJobs)
    {
        parent::__construct();
        $this->rosterAssignees = $rosterAssignees;
        $this->rosterJobs=$rosterJobs;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Roster Job Cron  Started");

        $today=date('Y-m-d');;
        if ($customDate = $this->argument('customDate')) {
            $today  = $customDate;
        }
        $rosterAssingnees=$this->rosterAssignees->getRoasterAssignees($today);
        foreach($rosterAssingnees as $rosterAssingnee) {
            $endDate= date_create($rosterAssingnee->end_date);
            $dueDate= date_create($rosterAssingnee->due_date);
            if(date_diff($dueDate,$endDate)->format("%R%a")>=0){
                $data=array('roster_assign_id'=>$rosterAssingnee->id,'status'=>0);
                $this->rosterJobs->create($data);
            };
            $newDue=new \DateTime($this->getDueDate($rosterAssingnee->due_date,$rosterAssingnee->frequency));
            $this->updateAssigneeAndSaveJob($rosterAssingnee->id,$newDue);
        }
    }

    public function getDueDate($dueDate,$days){

        $tempDate= date('Y-m-d', strtotime($dueDate. ' + '.$days.' days'));
        return $tempDate;
    }


    public function updateAssigneeAndSaveJob($rosterAssingneeId,$newDue){
        DB::beginTransaction();
        try {
            $response = $this->rosterAssignees->updateRosterAssigneeDueData($rosterAssingneeId, $newDue);
            if($response) {
                DB::commit();
            } else {
                DB::rollback();
                return array('success' => 'false', 'message' => 'Updating Roster Assignee failed');
            }
        } catch(Exception $ex){
            DB::rollback();
            return array('success' => 'false', 'message' => 'Someting went wrong');
        }
    }
}
