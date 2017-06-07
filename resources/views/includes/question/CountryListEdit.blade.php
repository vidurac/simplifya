<label class="col-sm-2 control-label">Country*</label>

<div class="col-sm-10">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control selectDrop_edit country" name="country" id="question_country_edit" disabled>

        <option value="">Choose...</option>
        @foreach($countries as $country)
            <?php $isExists= false; ?>
            @foreach($questionClassifications as $classification)
                @if($classification->entity_tag == "COUNTRY" && $country->id == $classification->option_value)
                    <?php $isExists= true; ?>
                @endif
            @endforeach

            @if($isExists)
                <option value="{{$country->id}}" selected>{{$country->name}}</option>
            @else
                <option value="{{$country->id}}">{{$country->name}}</option>
            @endif

        @endforeach

    </select>
</div>