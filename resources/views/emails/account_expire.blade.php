@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>We recently attempted to charge the payment method on your Simplifya account and were unable to successfully do so. Please log into Simplifya and update your card information to re-activate your account.</p>
    </p>
    <p>If you believe this to be an error, please contact us a contact@simplifya.com.</p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
