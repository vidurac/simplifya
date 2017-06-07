<label class="col-sm-2 control-label">Country*</label>
<div class="col-sm-3">
    <select class="form-control" name="country" id="question_country">
        @foreach($countries as $country)
            @if($country->id == "1")
                <option value="{{$country->id}}" selected>{{$country->name}}</option>
            @else
                <option value="{{$country->id}}">{{$country->name}}</option>
            @endif

        @endforeach

    </select>
</div>