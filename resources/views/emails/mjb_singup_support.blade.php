@extends('layout.email')
@section('content')
    <strong>NEW BUSINESS REGISTRATION</strong>
    <div>
        <p>{{$data['businessName']}}</p>
        <p>{{$data['name']}}</p>
        <p>{{$data['email']}}</p>
    </div>
    <br/>

    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection