@if(isset($supperParentQuestion))
    <?php $question_law=$supperParentQuestion->law; ?>
    @if (isset($question->law))
        <?php $question_law=$question->law;?>
    @endif
@else
    <?php $question_law=$question->law; ?>
@endif
<label class="col-sm-2 control-label">Law*</label>
<div class="col-sm-10">
    <input type="hidden" name="law" id="question_law_edit" value="{{$question_law}}">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control" name="law-d" id="question_law_edit-disable" disabled>
        @foreach($laws as $k=>$v)
            @if($k == $question_law)
                <option value="{{$k}}" selected>{{$v}}</option>
            @else
                <option value="{{$k}}">{{$v}}</option>
            @endif

        @endforeach
    </select>
</div>