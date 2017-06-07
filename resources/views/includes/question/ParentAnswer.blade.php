<label class="col-sm-2 control-label">Answers*</label>
<div class="col-sm-10">
    <div class="create_question_accordion" id="question_answer_id_{{$question_answer_id == 0 ? 0 : $question_answer_id."_".$count}}">

        @foreach($masterAnswers as $masterAnswer)
            <h3 master-answer-id="{{$masterAnswer->id}}" class="master_question_answer" question-answer-id="0" id="" >
                <input type="checkbox" checked="checked" class="create_qu_anser_click" id="create_qu_anser_click_{{$masterAnswer->id}}" value="{{$masterAnswer->id}}"> {{$masterAnswer->name}}
                <select id="answerValue_{{$masterAnswer->id}}_{{$question_answer_id == 0 ? 0 : $question_answer_id."_".$count}}" class="answerValue">

                    @if($masterAnswer->id == 1)
                        @foreach($masterAnswerValue as $value)
                            @if($value->id == 1 || $value->id == 2)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endif

                        @endforeach
                    @endif

                    @if($masterAnswer->id == 2)
                        @foreach($masterAnswerValue as $value)
                            @if($value->id == 2)
                                <option value="{{$value->id}}" selected>{{$value->name}}</option>
                            @endif

                            @if($value->id == 1)
                                <option value="{{$value->id}}" >{{$value->name}}</option>
                            @endif

                        @endforeach
                    @endif

                    @if($masterAnswer->id == 3)
                        @foreach($masterAnswerValue as $value)
                            @if($value->id == 3)
                                <option value="{{$value->id}}" selected>{{$value->name}}</option>
                            @endif
                        @endforeach
                    @endif
                    {{--@foreach($masterAnswerValue as $value)
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

                    @endforeach--}}
                </select>
            </h3>
            <div class="child_question_data">

            </div>



        @endforeach
    </div>
</div>