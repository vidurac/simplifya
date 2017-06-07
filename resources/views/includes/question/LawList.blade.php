<label class="col-sm-2 control-label">Law*</label>
<div class="col-sm-3">
    <select class="form-control" name="law" id="question_law">
        @foreach($laws as $k=>$v)
            @if($k == "1")
                <option value="{{$k}}" selected>{{$v}}</option>
            @else
                <option value="{{$k}}">{{$v}}</option>
            @endif

        @endforeach

    </select>
</div>