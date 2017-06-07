@extends('layout.email')
@section('content')
    <strong> Welcome to Simplifya!</strong>
    <p>Hi {{$data['name']}},</p>
    <p>Thank you for registering your business on Simplifya. To get started, please log into your account and fill out the information about your business, its staff, and the licenses your business holds. Once that is complete, you can get started on making sure your business is compliant!</p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
