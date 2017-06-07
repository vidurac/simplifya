<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CompanyRepository;
use DB;
use Carbon\Carbon;

class DeleteTemproraryMjbCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempMjb:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all unused temporary MJB companies';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CompanyRepository $company)
    {
        parent::__construct();
        $this->company = $company;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Deleting temporary MJB Cron  Started");
        try{
            \DB::beginTransaction();
            $tempMjbs=$this->selectAllTempMjbs();
            $actual_count=count($tempMjbs);
            $deleted_count=0;
            foreach($tempMjbs as $tempMjb){
                $company = $this->company->find($tempMjb->id);
                if($company){
                    $deleted_temp_mjb_location_license =$company->companyLocationLicense()->delete();
                    if($deleted_temp_mjb_location_license){
                        $deleted_temp_mjb_location=$company->companyLocation()->delete();
                        if($deleted_temp_mjb_location){
                            if ($company->delete()){
                                $deleted_count ++;
                            }
                        }
                    }
                }

            }

            if($actual_count  == $deleted_count )
            {
                \DB::commit();
            }
            else
            {
                $this->info("Could not delete all the temp mjb's");
                \DB::rollback();
            }

        }catch (Exception $e) {
            $this->info("Exception got fired " . $e->getMessage());
            \DB::rollback();
        }


    }

    private function selectAllTempMjbs(){
        return DB::table('companies')->select('companies.id')
            ->leftJoin('appointments','companies.id','=','appointments.to_company_id')
            ->where('companies.status','=','7')
            ->where('companies.created_at','<',DB::Raw('CURDATE()'))
            ->groupBy('companies.id')
            ->havingRaw('COUNT(appointments.to_company_id) = 0')
            ->get();

    }


}
