<label class="col-sm-2 control-label">Main Category*</label>

<div class="col-sm-3">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control" name="mainCategory" id="mainCategory_edit" classification-id="{{$mainCategoryOptions[0]->id}}">
        <option value="">Choose...</option>
        @foreach($mainCategoryOptions[0]->masterClassificationOptions as $option)
            <?php $isExists= false; ?>
            @foreach($questionClassifications as $classification)
                @if($option->classification_id == $classification->entity_tag && $option->id == $classification->option_value)
                    <?php $isExists= true; ?>
                @endif
            @endforeach

            @if($isExists)
                <option class="first_level" parent_id="{{$option->id}}" is_child="no" value="{{$option->id}}" selected> {{$option->name}}</option>
            @else
                @if($option->status == "1")
                    <option class="first_level" parent_id="{{$option->id}}" value="{{$option->id}}"> {{$option->name}}</option>
                @endif
            @endif

                @if(count($option->childs) > 0)
                    @foreach($option->childs as $child)

                        <?php $isSubExists = false; ?>
                        @foreach($questionClassifications as $sub_classification)

                            @if($child->id == $sub_classification->option_value && $sub_classification->entity_tag == "SUB_CATEGORY")
                                   {{-- <?php echo $child->id." ".$sub_classification->option_value; ?>--}}
                                <?php $isSubExists = true; ?>
                            @endif
                        @endforeach
                            @if($isSubExists)
                                <option class="sub_item " parent_id="{{$option->id}}" is_child="yes" value="{{$child->id}}" selected>&nbsp; &nbsp; {{$child->name}}</option>
                            @else
                                <option class="sub_item " parent_id="{{$option->id}}" is_child="yes" value="{{$child->id}}" > &nbsp; &nbsp; {{$child->name}}</option>
                            @endif


                    @endforeach
                @endif

        @endforeach

    </select>
</div>
