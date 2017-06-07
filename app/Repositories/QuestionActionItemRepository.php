<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class QuestionActionItemRepository extends Repository{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\QuestionActionItem';
    }
}