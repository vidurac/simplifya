@extends('layout.dashbord')

@section('content')

    <div class="content animate-panel">
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
                        Search Questions
                    </div>
                    <div class="panel-body">
                        <form method="get" class="form-horizontal" id="checklist-form">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Audit Type(s)</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="checklistAuditTypes" id="checklistAuditTypes">
                                        <option value="">All</option>
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
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Country</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="country" id="checklistCountry">
                                        <option value="0">All</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">State</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="state" id="checklistState">
                                        <option value="">All</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">City</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="cities" id="checklistCities">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <input type="checkbox" id="city_only" disabled > Only
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">License(s)</label>
                                <div class="col-sm-5">
                                    <select class="col-sm-12 padding0" name="licences" id="checklistLicences" multiple="multiple" data-placeholder="License(s)">

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Main Category</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="mainCategory" id="checklistMainCategory" classification-id="{{$mainCategoryOptions[0]->id}}">
                                        <option value=" ">All</option>
                                        @foreach($mainCategoryOptions[0]->masterClassificationOptions as $option)
                                            @if($option->status == "1")
                                                <option class="first_level" parent_id="{{$option->id}}" is_child="no" value="{{$option->id}}"> {{$option->name}}</option>
                                            @endif

                                            @if(count($option->childs) > 0)
                                                @foreach($option->childs as $child)
                                                    <option class="sub_item " parent_id="{{$option->id}}" is_child="yes" value="{{$child->id}}" > &nbsp; &nbsp; {{$child->name}} </option>
                                                @endforeach
                                            @endif
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Other Classifications</label>
                                <div class="col-sm-10">
                                    <table id="checklistClassifictionTable" class="col-sm-11">
                                        <thead>

                                        </thead>
                                        <tbody>
                                            @foreach($classifications as $classification)
                                                <tr>
                                                    <td> {{ $classification->name }} </td>
                                                    <td class="col-sm-9">
                                                        <select class="marginButtom1 col-sm-11 form-control form-group" name="req_classification_{{ $classification->id }}" id="req_classification_{{ $classification->id }}" classification-id="{{$classification->id}}">
                                                            <option value="">All</option>
                                                            @foreach($classification->masterClassificationOptions as $option)
                                                                <option value="{{ $option->id }}">{{ $option->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3 pull-right">
                                    <button class="btn w-xs btn-success pull-right" type="button" id="generateChecklist"><strong>Generate Checklist</strong></button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
                        Question Results Grid
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="checklist-detail-table">
                                        <thead>
                                        <tr>
                                            {{--<th>#</th>--}}
                                            <th>Question</th>
                                            <th>Created By</th>
                                            <th>Created Date & Time</th>
                                            <th>Detail</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{!! Html::script('/js/checklist/checklist.js') !!}
@stop