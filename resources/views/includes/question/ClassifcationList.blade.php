@if(count($classifications) > 0)
    <label class="col-sm-2 control-label" style="display:<?php ($enable == 'true')?print_r('block'):print_r('none');?>" >Other Classifications*</label>
    <div class="col-sm-10">
        <table id="reqClassifictionTable" class="col-sm-10 pull-left">
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Classification Type</th>--}}
                {{--<th>Options</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}

            <tbody>

            @foreach($classifications as $classification)
                @if($classification->status == "1")
                    <tr>
                        <td class="col-sm-2 p-b-s"> {{ $classification->name }} </td>
                        <td class="col-sm-8 p-b-s">
                            @if($classification->is_multiselect)
                                <select class="classification_option marginButtom1 col-sm-11 form-control padding0" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}" multiple="multiple">
                                    @foreach($classification->masterClassificationOptions as $option)
                                        @if($option->status == 1)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @else
                                <select class="marginButtom1 col-sm-11 form-control" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}">
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
