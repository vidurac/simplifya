@extends('layout.email')
@section('content')
    <strong> Account Deactivation</strong>
    <p>Hello {{$name}},</p>
    <p>Your Simplifya account has been deactivated. You can reactivate at any time simply by logging into your account and making a payment.</p><br>
    <p>If you did not deactivate your account, please contact Simplifya's customer service immediately.</p>
    <br>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
