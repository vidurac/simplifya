@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>
        You have successfully un-subscribe from your subscription plan. You can still log into Simplifya and re-activate your account.</p>
    </p>
    <p>If you believe this to be an error, please contact us a contact@simplifya.com.</p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
