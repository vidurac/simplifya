<?php $cityAllExists= false; ?>
@foreach($questionClassifications as $classification)
    @if($classification->entity_tag == "CITY" && $classification->option_value=="ALL")
        <?php $cityAllExists= true; ?>
    @endif
@endforeach
@if(isset($supperParentQuestion))
    <?php $question_law=$supperParentQuestion->law; ?>
    @if (isset($question->law))
        <?php $question_law=$question->law;?>
    @endif
@else
    <?php $question_law=$question->law; ?>
@endif
<label class="col-sm-2 control-label">Citi(es)*</label>
<div class="col-sm-10">
    @if($cityAllExists)
        <div class="city-wrap">
            <select class="padding0 selectDrop_edit" name="cities" id="question_cities_edit" multiple="multiple">
                @foreach($cities as $city)
                    <?php $isExists= false; ?>
                    @foreach($questionClassifications as $classification)
                        @if($classification->entity_tag == "CITY" && $city->id == $classification->option_value)
                            <?php $isExists= true; ?>
                        @endif
                    @endforeach

                    @if($isExists)
                        <option value="{{$city->id}}" selected>{{$city->name}}</option>
                    @else
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endif

                @endforeach
            </select>
        </div>
        <label id="chkCreateQuestionSelectAllCityError"></label>
        {{--<div>--}}
            {{--<input type="text" value="All" class="form-control" id="all_cities" readonly>--}}
        {{--</div>--}}
        {{--<div class="editQuestionSelectAllCity m-t">--}}
            {{--<input class="control-label" type="checkbox" id="chkEditQuestionSelectAllCity" checked> All cities in state--}}
        {{--</div>--}}
    @else
        <div class="city-wrap">
            <select class="padding0 selectDrop_edit " name="cities" id="question_cities_edit" multiple="multiple">
                @foreach($cities as $city)
                    <?php $isExists= false; ?>
                    @foreach($questionClassifications as $classification)
                        @if($classification->entity_tag == "CITY" && $city->id == $classification->option_value)
                            <?php $isExists= true; ?>
                        @endif
                    @endforeach

                    @if($isExists)
                        <option value="{{$city->id}}" selected>{{$city->name}}</option>
                    @else
                        <option value="{{$city->id}}">{{$city->name}}</option>
                    @endif

                @endforeach
            </select>
        </div>
        <label id="chkCreateQuestionSelectAllCityError"></label>
        {{--<div>--}}
            {{--<input type="text" value="All" class="form-control hidden" id="all_cities" readonly>--}}
        {{--</div>--}}
        {{--<div class="editQuestionSelectAllCity m-t">--}}
            {{--<input class="control-label" type="checkbox" id="chkEditQuestionSelectAllCity"> All cities in state--}}
        {{--</div>--}}
    @endif

</div>
