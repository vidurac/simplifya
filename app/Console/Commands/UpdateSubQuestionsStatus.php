<?php

namespace App\Console\Commands;

use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;

class UpdateSubQuestionsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_sub_question_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sub question where status is mismatch with parent';


    private $question, $questionClassification;

    /**
     * Create a new command instance.
     *
     * @param QuestionRepository $question
     * @param QuestionClassificationRepository $questionClassification
     */
    public function __construct(
        QuestionRepository $question,
        QuestionClassificationRepository $questionClassification)
    {
        parent::__construct();
        $this->question = $question;
        $this->questionClassification = $questionClassification;
    }
    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Update child questions status where parent status is mismatch");
        $questions = $this->questionClassification->getStatusMismatchSubQuestionsWithParent();
        // Start DB transaction
        DB::beginTransaction();
        try {
            foreach ($questions as $q) {
                $this->question->update(['status' => 0], $q->child_question_id);
            }
            DB::commit();
        }catch (Exception $e) {
            $this->info("Exception got fired " . $e->getMessage());
            DB::rollback();
        }
    }
}
