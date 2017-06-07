<label class="col-sm-2 control-label">Answers*</label>
<div class="col-sm-10">



    <div class="create_question_accordion" id="accordion_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}">


        @foreach($masterAnswers as $masterAnswer)
            <?php $answer_id=0; $answer_value=0; $answer_question_id = 0?>
            @foreach($questionAnswers as $questionAnswer)
                @if($masterAnswer->id == $questionAnswer->answer_id)
                    <?php $answer_id = $questionAnswer->id; ?>
                    <?php $answer_value = $questionAnswer->answer_value_id; ?>
                    <?php $answer_question_id = $questionAnswer->question_id; ?>
                @endif
            @endforeach

                @if($answer_id != 0)
                    <h3 master-answer-id="{{$masterAnswer->id}}"  answer-id="{{$answer_id}}" question-id="{{$questionId}}" view-only="1"  question-index="1"  class="accordion_open" id="header_{{$questionId}}_{{$answer_id}}">
                        <input disabled type="checkbox" class="create_qu_anser_click" answer-checkbox="create_qu_answer_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" id="create_qu_answer_click_{{$masterAnswer->id}}" value="{{$masterAnswer->id}}" answer-id="{{$answer_id}}" checked> {{$masterAnswer->name}}

                        <select class="answerValue" disabled>
                            @foreach($masterAnswerValue as $value)
                                @if($value->id == $answer_value)
                                    <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                @else
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endif
                            @endforeach
                        </select>

                    </h3>
                @else
                    <h3 master-answer-id="{{$masterAnswer->id}}" answer-id="0" answer-question-id="{{$answer_question_id}}" question-id="{{$questionId}}" view-only="1" class="accordion_open">
                        <input disabled type="checkbox" class="create_qu_anser_click" answer-checkbox="create_qu_answer_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" id="create_qu_answer_click_{{$masterAnswer->id}}" value="{{$masterAnswer->id}}" answer-id="{{$answer_id}}"> {{$masterAnswer->name}}

                        <select class="answerValue" disabled>
                            @foreach($masterAnswerValue as $value)
                                @if($masterAnswer->id == 2)
                                    @if($value->id == 2)
                                        <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                    @else
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endif
                                @elseif($masterAnswer->id == 3)
                                    @if($value->id == 3)
                                        <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                    @else
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endif
                                @else
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </h3>
                @endif



                <div class="child_question_data">
                    <?php $count = 0; ?>
                    @foreach($questions as $key => $question)
                        @if($question->question_answer_id == $answer_id)
                            <div id="answer_question_{{$answer_id}}_{{$answer_question_id}}_{{$question->id}}" answer-id="{{$answer_id}}" answer-question-id="{{$answer_question_id}}" question-id="{{$question->id}}" class="answer_question">
                            </div>
                            <?php $count++; ?>
                        @endif
                    @endforeach

                    @if($count == 0)
                        <div id="answer_question_{{$answer_id}}_{{$answer_question_id}}_0" answer-id="{{$answer_id}}" answer-question-id="{{$answer_question_id}}" question-id="0" class="answer_question" >
                        </div>
                    @endif
                    <div id="answer_question_{{$answer_id}}_{{$answer_question_id}}_common"></div>
                </div>

        @endforeach
    </div>
</div>