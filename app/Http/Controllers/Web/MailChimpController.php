<?php

namespace App\Http\Controllers\Web;

use App\Repositories\CompanyRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mailchimp;
use App\Repositories\MasterEntityTypeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Lib\CsvGenerator;

class MailChimpController extends Controller
{
    private $mailchimp, $entityType, $company, $user,$csv;
    /**
     * Pull the Mailchimp-instance (including API-key) from the IoC-container.
     */
    public function __construct(Mailchimp $mailchimp, MasterEntityTypeRepository $entityType, CompanyRepository $company, UsersRepository $user, CsvGenerator $csv)
    {
        $this->mailchimp = $mailchimp;
        $this->entityType = $entityType;
        $this->company = $company;
        $this->user = $user;
        $this->csv = $csv;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_id = Auth::user()->master_user_group_id;


        // if supper admin
        if($group_id == Config::get('simplifya.MasterAdmin')){
            $entityTypes = $this->entityType->all();
            $companies = $this->company->getAllCompanies();

            return view('mailChimp.index')->with(array('entityTypes' => $entityTypes, 'companies' => $companies, 'page_title' => 'MailChimp Manager'));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }

    }


    /**
     * Get MailChimp Mail List

     * @return json
     */
    public function getMailList(){
        $result = $this->mailchimp->lists->getList();
        return response()->json(array("data" => $result));
    }

    /**
     * Sync with MailChimp

     * @return json
     */
    public function syncUsers(){
        $list = $_POST['list'];
        $emails = $_POST['emails'];

        foreach($emails as $email){
            try {
                $this->mailchimp->lists->subscribe($list, ['email' => $email]);

            } catch (\Mailchimp_List_AlreadySubscribed $e) {
                // do something
            } catch (\Mailchimp_Error $e) {
                // do something
            }
        }

        return response()->json(array("message" => 'success'));
    }


    /**
     * Get all users
     *
     * @return json
     */
    public function getAllUsers(){
        $data = array();
        $entityType = $_GET["entityType"];
        $companyList = $_GET["companyList"];

        $users = $this->user->findMailChimpUsers($companyList);

        foreach($users as $user){

            $entityTypeVal = $this->entityType->find($user["masterUserGroup"]["entity_type_id"]);
            if($entityType != ""){
                if ($user["masterUserGroup"]["entity_type_id"] == $entityType){
                    $data[] = array(
                        $row[] =   "<input type='checkbox' class='chkMailChimpUser' value='".$user['email']."'>",
                        $user['name'],
                        $user['email'],
                        $user['masterUserGroup']["name"],
                        $user['company']["name"],
                        $entityTypeVal->name,
                    );
                }
            }
            else{
                $data[] = array(
                    $row[] =  "<input type='checkbox' class='chkMailChimpUser' value='".$user['email']."'>",
                    $user['name'],
                    $user['email'],
                    $user['masterUserGroup']['name'],
                    $user['company']["name"],
                    $entityTypeVal->name,
                );
            }
        }

        return response()->json(["data" => $data]);

    }


    /**
     * Get all company users
     *
     * @return json
     */

    public function getAllCompanyUsers(){
        $data = array();
        $entityType = $_GET["entityType"];
        $companyList = $_GET["companyList"];
        $user_name = $_GET["user_name"];

        $users = $this->user->findCompanyUsers($entityType, $companyList, $user_name);

        foreach($users as $user){

            $entityTypeVal = $this->entityType->find($user["masterUserGroup"]["entity_type_id"]);
            if($entityType != ""){
                if ($user["masterUserGroup"]["entity_type_id"] == $entityType){
                    $data[] = array(
                        $user['name'],
                        $user['email'],
                        $user['masterUserGroup']["name"],
                        $user['company']["name"],
                        $entityTypeVal->name,
                    );
                }
            }
            else{
                $data[] = array(
                    $user['name'],
                    $user['email'],
                    $user['masterUserGroup']['name'],
                    $user['company']["name"],
                    $entityTypeVal->name,
                );
            }
        }

        return response()->json(["data" => $data]);

    }


    /**
     * get company list

     * @return json
     */
    public function companyList(){
        $entityType = $_GET['entityType'];
        if($entityType == 0){
            $companies = $this->company->where('status','!=','7')->all();
        }
        else{
            $companies = $this->company->where('status','!=','7')->findWhere(array("entity_type" => $entityType));
        }

        return response()->json(array("data" => $companies));
    }

    public function exportCompanyUserSearchResults()
    {
        //declare and initialize variables
        $user_name = Input::get('user_name');
        $entityType = Input::get('entity_type');
        $companyList = Input::get('company_list');

        $users = $this->user->findCompanyUsers($entityType, $companyList, $user_name);
        $data = array();
        foreach($users as $user){

            $entityTypeVal = $this->entityType->find($user["masterUserGroup"]["entity_type_id"]);
            if($entityType != ""){
                if ($user["masterUserGroup"]["entity_type_id"] == $entityType){
                    $data[] = array(
                        $user['name'],
                        $user['email'],
                        $user['masterUserGroup']["name"],
                        $user['company']["name"],
                        $entityTypeVal->name,
                    );
                }
            }
            else{
                $data[] = array(
                    $user['name'],
                    $user['email'],
                    $user['masterUserGroup']['name'],
                    $user['company']["name"],
                    $entityTypeVal->name,
                );
            }
        }

        // Define headers
        $headers = ['Name', 'Email', 'User Group', 'Company', 'Entity Type'];
        // Define file name
        $filename = "CompaniesList.csv";
        // Create CSV file
        return $this->csv->create($data, $headers, $filename);

    }
}
