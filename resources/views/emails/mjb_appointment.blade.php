@extends('layout.email')
@section('content')
    <strong> Audit Appointment</strong>
    <p>Your company has received an audit appointment from {{$data['entity_type']}}. Appointment details as follow. </p>

    <p>Company Name : {{$data['from_company']}}</p>
    <p>Marijuana Business Name : {{$data['to_company']}}</p>
    <p>Location to be Audited : </p>
    <p>
        {{$data['location_name']}}
        <br>
        <?php echo trim($data['address_line_1']);?>{{($data['address_line_2'] !='')?', '.$data['address_line_2'].'.':'.'}}
        <br/>
        {{$data['city']}}, {{$data['state']}} {{($data['zip_code'] != '')?$data['zip_code']:''}}
    </p>

    <p>Inspector: {{$data['assign_to']}}</p>

    <p>Date and Time: {{$data['inspection_Date']}} at {{$data['inspection_Time']}}</p>

    <p>Comments : {{$data['comment']}}</p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection

