<?php

namespace App\Console\Commands;

use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;

/**
 * Class UpdateDeletedSubQuestions
 *
 * Scenario:
 *  - Parent Question `A` has two child questions `A1` and `A2`
 *  - Delete the parent question `A`.
 *  - When we delete the parent question it should delete all child questions as well.
 *  - But the system does not delete child questions.
 *
 * This artisan command task will fetch all child questions and updated them
 * as deleted which have not deleted when parent got delete.
 *
 * @package App\Console\Commands
 */
class UpdateDeletedSubQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_is_deleted_on_sub_question';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update is_deleted 1 on sub question';


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
        $this->info("Update child questions where parent already been deleted");
        $questions = $this->questionClassification->getDeletedChildQuestionMapping();
        // Start DB transaction
        DB::beginTransaction();
        try {
            foreach ($questions as $q) {
                $this->question->update(['is_deleted' => 1], $q->child_question_id);
            }
            DB::commit();
        }catch (Exception $e) {
            $this->info("Exception got fired " . $e->getMessage());
            DB::rollback();
        }
    }
}
