<label class="col-sm-2 control-label">Citi(es)*</label>
<?php $cityAllExists= false; ?>
@foreach($questionClassifications as $classification)
    @if($classification->entity_tag == "CITY" && $classification->option_value=="ALL")
        <?php $cityAllExists= true; ?>
    @endif
@endforeach
@if($cityAllExists)
    <div class="col-sm-3">
        <input type="text" value="All" class="form-control" id="all_cities" readonly>
    </div>

    <div class="col-sm-10 city-wrap hidden">
        <select {{isset($viewOnly)? 'disabled' : ''}} class="padding0 selectDrop_edit cities city_main" name="cities" id="question_cities_edit" disabled multiple="multiple">


            {{--@if($isGENERALExists == false)--}}
            {{--@if(isset($is_parent) && $is_parent == 1)
                <option value="GENERAL" >GENERAL</option>
            @endif--}}

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
        @if(isset($citiesH))
            <input type="hidden" id="city_idsH" value="{{json_encode($citiesH)}}" />
        @else
            <input type="hidden" id="city_idsH" value="" />
        @endif
        <label id="chkCreateQuestionSelectAllCityError"></label>
    </div>

    {{--<div class="col-sm-offset-2 col-sm-10 m-t editQuestionSelectAllCity">--}}
        {{--@if(!isset($viewOnly))--}}
            {{--<input class="control-label" type="checkbox" id="chkEditQuestionSelectAllCity" checked> All cities in state--}}
        {{--@endif--}}
    {{--</div>--}}



@else
    <div class="col-sm-3">
        <input type="text" value="All" class="form-control hidden" id="all_cities" readonly>
    </div>

    <div class="col-sm-10 city-wrap">
        <select {{isset($viewOnly)? 'disabled' : ''}} class="padding0 selectDrop_edit cities city_main" name="cities" id="question_cities_edit" multiple="multiple">


            {{--@if($isGENERALExists == false)--}}
            {{--@if(isset($is_parent) && $is_parent == 1)
                <option value="GENERAL" >GENERAL</option>
            @endif--}}

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
        @if(isset($citiesH))
            <input type="hidden" id="city_idsH" value="{{json_encode($citiesH)}}" />
        @else
            <input type="hidden" id="city_idsH" value="" />
        @endif
        <label id="chkCreateQuestionSelectAllCityError"></label>
    </div>

    {{--<div class="col-sm-offset-2 col-sm-10 m-t editQuestionSelectAllCity">--}}
        {{--@if(!isset($viewOnly))--}}
            {{--<input class="control-label" type="checkbox" id="chkEditQuestionSelectAllCity"> All cities in state--}}
        {{--@endif--}}
    {{--</div>--}}
@endif


