<label class="col-sm-2 control-label">Keywords</label>
<div class="col-sm-10">
    <select class="col-sm-12 padding0" name="keywords" id="create_keywords" multiple="multiple">
        @foreach($masterKeywords as $option)
            <option value="{{$option->id}}"> {{$option->name}}</option>
        @endforeach
    </select>
</div>