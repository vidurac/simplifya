@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>Your account has been expired. Please make the payment and activate the account.</p>
    </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection