<label class="col-sm-2 control-label">Audit Type(s)*</label>

<div class="col-sm-10">
    <select {{isset($viewOnly)? 'disabled' : ''}} class="col-sm-12 padding0 selectDrop_edit auditType" name="auditType" id="auditTypes_edit" multiple="multiple">

        @foreach($auditTypes as $auditType)
            <?php $isExists= false; ?>
            @foreach($questionClassifications as $classification)
                @if($classification->entity_tag == "AUDIT_TYPE" && $auditType->id == $classification->option_value)
                    <?php $isExists= true; ?>
                @endif
            @endforeach

            @if($isExists)
                    @if($auditType->name == 'In-house')
                        <option value="{{$auditType->id}}" selected> Self-audit</option>
                    @endif
                    @if($auditType->name == '3rd-Party')
                        <option value="{{$auditType->id}}" selected> {{$auditType->name}}</option>
                    @endif
            @else
                    @if($auditType->name == 'In-house')
                        <option value="{{$auditType->id}}"> Self-audit</option>
                    @endif
                    @if($auditType->name == '3rd-Party')
                        <option value="{{$auditType->id}}"> {{$auditType->name}}</option>
                    @endif
            @endif

        @endforeach
    </select>
</div>
