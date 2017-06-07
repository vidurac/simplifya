<label class="col-sm-2 control-label">State*</label>
<div class="col-sm-10">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control selectDrop_edit state" name="state" id="question_state_edit">
        <option value="">Choose...</option>
        @foreach($states as $state)
            <?php $isExists= false; ?>
            @foreach($questionClassifications as $classification)
                @if($classification->entity_tag == "STATE" && $state->id == $classification->option_value)
                    <?php $isExists= true; ?>
                @endif
            @endforeach

            @if($isExists)
                <option value="{{$state->id}}" selected>{{$state->name}}</option>
            @else
                <option value="{{$state->id}}">{{$state->name}}</option>
            @endif

        @endforeach

    </select>
</div>
