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
                <option value="{{$option->id}}" selected> {{$option->name}}</option>
            @else
                @if($option->status == "1")
                    <option value="{{$option->id}}"> {{$option->name}}</option>
                @endif
            @endif

        @endforeach

    </select>
</div>