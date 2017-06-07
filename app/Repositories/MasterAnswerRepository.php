<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/5/2016
 * Time: 4:06 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\Hash;


class MasterAnswerRepository extends Repository
{
    public function model() {
        return 'App\Models\MasterAnswer';
    }
}