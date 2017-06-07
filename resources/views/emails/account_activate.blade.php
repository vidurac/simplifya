@extends('layout.email')
@section('content')
    <strong> Account Activation </strong>
    <p>Hello {{$name}},</p>
    <p>Your Simplifya account has been activated.</p>
    <br>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection