@extends('layout.email')
@section('content')
    <strong> Audit Report Finalization </strong>
    <p>The report for audit #{{$data['inspectionNumber']}} has been finalized and is ready to view.</p>
    <p>
        @if($data['locationName'])
            {{$data['locationName']}}<br/>
        @endif
        @if($data['auditType'])
            {{$data['auditType']}}<br/>
        @endif
        @if($data['auditorName'])
            {{$data['auditorName']}} @if($data['auditorCompany']), {{$data['auditorCompany']}}@endif<br/>
        @endif
        Date and Time: {{$data['inspection_Date']}} at {{$data['inspection_Time']}}<br/>
        @if($data['allLicenses'] && $data['allLicenses'] != '')
        {{$data['allLicenses']}}
        @endif
    </p>



    <p>To view the report, click the button below.</p>
    <br />
    <center>
        <a style="border-radius:3px;color:white;font-size:15px;padding:14px 7px 14px 7px;max-width:210px;font-family:proxima_nova,'Open Sans','lucida grande','Segoe UI',arial,verdana,'lucida sans unicode',tahoma,sans-serif;border:1px #1373b5 solid;text-align:center;text-decoration:none;width:210px;margin:6px auto;display:block;background-color:#007ee6" href="{{ url('/report/edit/'.$data['appointmentId']) }}" target="_blank">View Report</a>
    </center>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} Team</p>
@endsection
