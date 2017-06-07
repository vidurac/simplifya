@extends('layout.email')
@section('content')
    <strong> Audit Notification </strong>
    <p>You have been assigned to an audit appointment: </p>
    <p>
        {{$data['to_company']}}
        <br>
        {{$data['location_name']}}
        <br>
        <?php echo trim($data['address_line_1']);?>{{($data['address_line_2'] !='')?', '.$data['address_line_2'].'.':'.'}}
        <br/>
        {{$data['city']}}, {{$data['state']}} {{($data['zip_code'] != '')?$data['zip_code']:''}}
    </p>

    <p>Audit date : {{$data['inspection_Date']}} <br/>
        Audit time : {{$data['inspection_Time']}}</p>

    @if(!empty($data['comment']))<p><b>COMMENTS :</b> {{$data['comment']}}</p>@endif

    <p>Some facilities will not have internet access available, so before arriving at your audit, you will need to log into the Simplifya iPad app, download the audit checklist, and make sure you're still logged into the app before arriving at the audit location. </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} Team</p>
@endsection
