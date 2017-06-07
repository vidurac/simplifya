@extends('layout.email')
@section('content')
    <strong> License Renewal Reminder </strong>
    <p>Hello {{$name}},</p>
    <p>This email serves as a reminder that the following license will need to be renewed in {{$data['no_of_day']}} days.</p>
    <p>
        License Type : {{$data['license_type']}}<br/>
        License Number : {{$data['license_number']}}<br/>
        Renewal Date : {{$data['expiry_date']}}<br/>
        Expiration Date : {{$data['license_date']}}<br/>
    </p>
    <br>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection