@if(count($classifications) > 0)
    <label class="col-sm-2 control-label">Other Classifications*</label>
    <div class="col-sm-10">
        <table id="reqClassifictionTable_edit" class="col-sm-10 pull-left">
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Classification Type</th>--}}
                {{--<th>Options</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}

            <tbody>
            @foreach($classifications as $classification)

                <?php $isClassificationExists= false; ?>
                @foreach($questionClassifications as $questionClassification)
                    @if($questionClassification->entity_tag == $classification->id)
                        <?php $isClassificationExists= true; ?>
                    @endif
                @endforeach

                @if($isClassificationExists || $classification->status == "1")
                    <tr>
                        <td class="col-sm-2 p-b-s"> {{ $classification->name }} </td>
                        <td class="col-sm-8 p-b-s">
                            @if($classification->is_multiselect)
                                @if($classification->status == "1")
                                    <select {{isset($viewOnly)? 'disabled' : ''}} class="selectDrop_edit multi_select classification_option_edit marginButtom1 col-sm-11 form-control req_classification padding0" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}" multiple="multiple">
                                @else
                                    <select {{isset($viewOnly)? 'disabled' : ''}} class="selectDrop_edit multi_select classification_option_edit marginButtom1 col-sm-11 form-control req_classification padding0" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}" multiple="multiple"  disabled="disabled">
                                @endif

                                        @foreach($classification->masterClassificationOptions as $option)
                                            <?php $isExists= false; ?>
                                            @foreach($questionClassifications as $questionClassification)
                                                @if($questionClassification->entity_tag == $classification->id && $option->id == $questionClassification->option_value)
                                                    <?php $isExists= true; ?>
                                                @endif
                                            @endforeach

                                            @if($isExists)
                                                <option value="{{ $option->id }}" selected>{{ $option->name }}</option>
                                            @else
                                                @if($option->status == "1")
                                                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                                                @endif
                                            @endif

                                        @endforeach
                                    </select>
                            @else
                                @if($classification->status == "1")
                                    <select {{isset($viewOnly)? 'disabled' : ''}} class="selectDrop_edit marginButtom1 col-sm-11 form-control req_classification" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}">
                                @else
                                    <select {{isset($viewOnly)? 'disabled' : ''}} class="selectDrop_edit marginButtom1 col-sm-11 form-control req_classification" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}" disabled="disabled">
                                @endif

                                        <option value="">Choose...</option>
                                        @foreach($classification->masterClassificationOptions as $option)
                                            <?php $isExists= false; ?>
                                            @foreach($questionClassifications as $questionClassification)
                                                @if($questionClassification->entity_tag == $classification->id && $option->id == $questionClassification->option_value)
                                                    <?php $isExists= true; ?>
                                                @endif
                                            @endforeach

                                            @if($isExists)
                                                <option value="{{ $option->id }}" selected>{{ $option->name }}</option>
                                            @else
                                                @if($option->status == "1")
                                                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                                                @endif
                                            @endif

                                        @endforeach
                                    </select>
                            @endif
                        </td>
                    </tr>
                @endif



            @endforeach


            </tbody>
        </table>
    </div>
@endif
