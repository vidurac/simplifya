<label class="col-sm-2 control-label">Audit Type(s)*</label>
<div class="col-sm-3">
    <select class="col-sm-12 padding0" name="auditType" id="auditTypes" multiple="multiple">
        @foreach($auditTypes as $auditType)
            @if($auditType->name == 'In-house')
                <option value="{{$auditType->id}}"> Self-audit</option>
            @endif
            @if($auditType->name == '3rd-Party')
                 <option value="{{$auditType->id}}"> {{$auditType->name}}</option>
            @endif

        @endforeach
    </select>
</div>