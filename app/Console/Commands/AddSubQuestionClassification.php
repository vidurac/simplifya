<?php

namespace App\Console\Commands;

use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class AddSubQuestionClassification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sub_question_classification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add sub question classification into question_classifications';


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
        $this->info("Migrating old sub question classification into question_classification");

        $questions = $this->question->findAllBy('parent_question_id', 0);

        // Loop all parent question
        foreach($questions as $question) {
            $this->info("{$question->id} - {$question->question}");

            // Find all sub question which is belongs to parent
            $subQuestions = $this->question->findOnlySubQuestions($question);
            // Loop all sub questions

            $classifications = $this->questionClassification->findWhere(array('question_id' => $question->id));

            foreach ($subQuestions as $subQuestion) {
                $classificationsExists = $this->questionClassification->findWhere(array('question_id' => $subQuestion->id));
                //$this->info("\t{$subQuestion->id} - {$subQuestion->question}");

                if (!count($classificationsExists)) {
                    //$this->info("\t classification record NOT exists!");
                    // create classifications
                    foreach($classifications as $classification){
                        $data = array(
                            'question_id' => $subQuestion->id,
                            'entity_tag' => $classification->entity_tag,
                            'option_value' => $classification->option_value,
                            "created_by" => $question->created_by,
                            "updated_by" => $question->updated_by
                        );
                        $this->questionClassification->create($data);
                    }
                }else {
                    //$this->info("\t classification record EXIST!");
                }
            }
        }
    }
}
