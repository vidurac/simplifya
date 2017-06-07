@extends('layout.email')
@section('content')
    <strong> PAYMENT PENDING NOTIFICATION</strong>
    <div>
        <p>The following business signed up 48 hours ago and have not entered payment details yet:</p>
        <p>{{$data['businessName']}}</p>
        <p>{{$data['name']}}</p>
        <p>{{$data['email']}}</p>
        <p>{{$data['contact']}}</p>
        <p>Account created on: {{$data['createdDate']}} at {{$data['createdTime']}}</p> <br/>
    </div>
    <br/>

    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection