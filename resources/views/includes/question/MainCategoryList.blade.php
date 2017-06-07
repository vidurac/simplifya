<label class="col-sm-2 control-label">Main Category*</label>
<div class="col-sm-3">

    <select class="form-control" name="mainCategory" id="mainCategory" classification-id="{{$mainCategoryOptions[0]->id}}">
        <option value="">Choose...</option>
        @foreach($mainCategoryOptions[0]->masterClassificationOptions as $option)
            @if($option->status == "1")
                <option class="first_level" parent_id="{{$option->id}}" is_child="no" value="{{$option->id}}"> {{$option->name}}</option>
            @endif

                @if(count($option->childs) > 0)
                    @foreach($option->childs as $child)
                        <option class="sub_item " parent_id="{{$option->id}}" is_child="yes" value="{{$child->id}}" > &nbsp; &nbsp; {{$child->name}}</option>
                    @endforeach
                @endif
        @endforeach

    </select>
</div>