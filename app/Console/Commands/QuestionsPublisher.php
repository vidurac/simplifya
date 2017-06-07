<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Repositories\QuestionRepository;
use Carbon\Carbon;

class QuestionsPublisher extends Command
{

    private $question;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question_publisher {customDate? : (optional) The date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish questions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(QuestionRepository $question)
    {
        parent::__construct();
        $this->question = $question;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];
        $current_time = Carbon::now()->format('Y-m-d');
        if ($customDate = $this->argument('customDate')) {
            $current_time  = $customDate;
        }

        $unPublishedQuestions = $this->question->getUnPublishedQuestions($current_time);
        foreach ($unPublishedQuestions as $unPublishedQuestion)
        {
           $data[] = $unPublishedQuestion->id;
        }

        $this->question->updateUnPublishedQuestions($data);
    }
}