<div>
    @if (isset($parentQuestionId))
        <?php $count = 0; ?>
        <?php foreach ($questions as $q): ?>
            <div class="">
                <div class="row">
                    <div class="col-md-9">
                        <div class="custom-info-alert-panel custom-info-alert-question">{{$q->indexValue}} {{$q->question}}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="sub-question-edit-actions">
                            <a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="View" href="{{URL('question/edit/child/'.$q->id.'/0')}}"><i class="fa fa-eye"></i></a>
                            <?php if ($viewOnly == '0'): ?>
                                <a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="Edit" href="{{URL('question/edit/child/'.$q->id.'/1')}}"><i class="fa fa-paste"></i></a>
                                @if ($q->status != 1)
                                    <a class="btn btn-warning btn-circle btn-xm" onclick="changeQuestionStatus({{$q->id}}, 1, 1, {{$questionAnswerId}}, {{$parentQuestionId}})" data-toggle="tooltip" title="Active"><i class="fa fa-thumbs-o-up"></i></a>
                                @else
                                    <a class="btn btn-success btn-circle btn-xm" onclick="changeQuestionStatus({{$q->id}}, 0, 1, {{$questionAnswerId}}, {{$parentQuestionId}})" data-toggle="tooltip" title="Active"><i class="fa fa-thumbs-o-down"></i></a>
                                @endif
                                <a class="btn btn-danger btn-circle btn-xm" data-toggle="tooltip" title="Delete" onclick="deleteQuestion({{$q->id}}, '{{$q->indexValue}}', {{$questionAnswerId}})"><i class="fa fa-trash-o"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                        $parentQuestionId = $q->parent_question_id;
                        $questionId = $q->id;
                    ?>
                    {{--<label class="col-sm-2 control-label">Answers*</label>--}}
                    <div class="panel-body">
                        <div class="create_question_accordion" id="accordion_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}">
                            @foreach($masterAnswers as $masterAnswer)
                                <?php $questionAnswers = $q->questionAnswers; ?>
                                <?php $answer_id=0; $answer_value=0; $answer_question_id = 0?>
                                @foreach($questionAnswers as $questionAnswer)
                                    @if($masterAnswer->id == $questionAnswer->answer_id)
                                        <?php $answer_id = $questionAnswer->id; ?>
                                        <?php $answer_value = $questionAnswer->answer_value_id; ?>
                                        <?php $answer_question_id = $questionAnswer->question_id; ?>
                                    @endif
                                @endforeach
                                    @if($answer_id != 0)
                                        <h3 master-answer-id="{{$masterAnswer->id}}"  answer-id="{{$answer_id}}" question-id="{{$questionId}}" question-index="{{$q->indexValue}}" view-only="{{$viewOnly}}" class="accordion_open" id="header_{{$questionId}}_{{$answer_id}}">
                                            <input type="checkbox" class="create_qu_anser_click" answer-checkbox="create_qu_answer_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" id="create_qu_answer_click_{{$masterAnswer->id}}" value="{{$masterAnswer->id}}" answer-id="{{$answer_id}}" checked> {{$masterAnswer->name}}

                                            <select class="answerValue">
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
                                        <h3 master-answer-id="{{$masterAnswer->id}}" answer-id="0" answer-question-id="{{$answer_question_id}}" question-id="{{$questionId}}"  view-only="{{$viewOnly}}" class="accordion_open">
                                            <input type="checkbox" class="create_qu_anser_click" answer-checkbox="create_qu_answer_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" id="create_qu_answer_click_{{$masterAnswer->id}}" value="{{$masterAnswer->id}}" answer-id="{{$answer_id}}"> {{$masterAnswer->name}}

                                            <select class="answerValue">
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
                        <script>
                            $(function(){
                                var qaId = "<?php echo $questionAnswerId; ?>";
                                var pqId = "<?php echo $parentQuestionId; ?>";
                                var qId = "<?php echo $questionId; ?>";
                                $("#accordion_"+qaId+"_"+pqId+"_"+qId).accordion({collapsible: true, active : 'none', heightStyle: "content"});
                            });
                        </script>
                    </div>
                </div>
            </div>
            <?php ++$count;?>
        <?php endforeach; ?>
    @endif
    <div class="padder-v add-new-sub-question-wrapper">
        <?php if ($viewOnly == '0'): ?>
        <a href="{{URL('question/create/child/' . $parentQuestionId .'/' . $questionAnswerId)}}" class="create_parent_question btn w-xs btn-success">Add Child Question</a>
        <?php endif; ?>
    </div>
</div>