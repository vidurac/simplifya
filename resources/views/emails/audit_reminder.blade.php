@extends('layout.email')
@section('content')
    <p><b>Audit Reminder</b></p>
    <p>Hi {{$name}},</p>
    <p>This is just a reminder that you are scheduled to conduct an audit in 48 hours:</p>
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

    <br>
    <p>Some facilities will not have internet access available, so before arriving at your audit, you will need to log into the Simplifya iPad app, download the audit checklist, and make sure you're still logged into the app before arriving at the audit location.</p>
    <br>
    <p>Thanks,<br/>
        The {{$data['company']}} team</p>
@endsection