<label class="col-sm-2 control-label">Keywords</label>
<div class="col-sm-10">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="col-sm-12 padding0" name="keywords" id="edit_keywords" multiple="multiple">
        @foreach($masterKeywords as $option)
            <?php $isExists= false; ?>
            @foreach($keywordList as $keyword)
                @if($option->id == $keyword->keyword_id)
                    <?php $isExists= true; ?>
                @endif
            @endforeach

            @if($isExists)
                <option value="{{$option->id}}" selected> {{$option->name}}</option>
            @else
                <option value="{{$option->id}}"> {{$option->name}}</option>
            @endif

        @endforeach
    </select>
</div>