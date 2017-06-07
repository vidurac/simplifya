@if(count($classificationsNotReq) > 0)
    <label class="col-sm-2 control-label" style="display:<?php ($not_req_enable)?print_r('block'):print_r('none');?>">Other Classifications</label>
    <div class="col-sm-10">
        <table id="nonReqClassifictionTable" class="col-sm-10 pull-left">
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Classification Type</th>--}}
                {{--<th>Options</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}

            <tbody>



            @foreach($classificationsNotReq as $classification)
                @if($classification->status == "1")
                    <tr>
                        <td class="col-sm-2 p-b-s"> {{ $classification->name }} </td>
                        <td class="col-sm-8 p-b-s">
                            @if($classification->is_multiselect)
                                <select class="not_req_classification classification_option_not_req marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="not_req_classification_{{ $classification->id }}" id="not_req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}" multiple="multiple">
                                    @foreach($classification->masterClassificationOptions as $option)
                                        @if($option->status == 1)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @else
                                <select class="not_req_classification marginButtom1 col-sm-11 form-control selectDrop_edit" name="not_req_classification_{{ $classification->id }}" id="not_req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}">
                                    <option value="">Choose...</option>
                                    @foreach($classification->masterClassificationOptions as $option)
                                        @if($option->status == 1)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
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
