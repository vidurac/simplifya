@extends('layout.email')
@section('content')
    <strong>Audit Request</strong>
    <p>Your company has received an audit request from {{ $data['mjb_name'] }}</p><br/>
    <p>Business Name : {{ $data['mjb_name'] }}<br>
        Location to be Audited : {{ $data['mjb_location_name'] }} <br/>
        Address :{{$data['mjb_location_address_1']}}{{($data['mjb_location_address_2'] != '')?','.$data['mjb_location_address_2']:'.'}}, {{$data['mjb_location_city']}}, {{$data['mjb_location_state']}} {{$data['mjb_location_zip_code']}}<br>
        Phone : {{$data['mjb_location_contact_no']}}<br>
        Comment : <?php echo $data['request_note'] ?> </p><br/>
    <p>
        Please contact {{ $data['mjb_name'] }} at the phone number provided to work out the scheduling and pricing details of the audit to be performed. Once everything has been arranged, please log into your Simplifya account, schedule the appointment, and assign someone to conduct the audit. Audits are conducted using Simplifya's iPad app.
    </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['system']}} Team</p>
@endsection