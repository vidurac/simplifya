@extends('layout.email')
@section('content')
    <strong> Simplifya Referral Code</strong>
    <p>Hello {{$name}},</p>
    <p>A referral code has been generated for you to share. Each time a business signs up on Simplifya using your referral code, they will enjoy a discount of {{$data['amount']}} per license. You’ll also earn a commission for every month the business is on Simplifya!</p>
    <p>Your referral code is: {{$data['code']}} </p>
    <p>You can also share the following unique link. When clicked, the Simplifya sign-up page will automatically populate with your referral code. </p>
    <p>{{$data['link']}} </p>
    <br>
    <p>Thanks,<br/>
        The {{$data['company']}} team</p>
@endsection