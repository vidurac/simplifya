@extends('layout.email')
@section('content')
    <strong> NO SELF-AUDIT CREATED 48 HOURS AFTER PAYMENT MADE</strong>
    {{--<strong> MJB no audit made notification</strong>--}}
    <div>
        {{--<p>Following MJB account has made the payment, but no audit made yet.</p>--}}
        {{--<p>Name - {{$data['name']}}</p>--}}
        {{--<p>Email - {{$data['email']}}</p>--}}
        {{--<p>Business Name- {{$data['businessName']}}</p>--}}
        {{--<p>Business Registration Name- {{$data['reg_no']}}</p>--}}
        {{--<p>Account created on: {{$data['createdDate']}} at {{$data['createdTime']}}</p> <br/>--}}

        <p>{{$data['businessName']}}<br/>
        {{$data['name']}}<br/>
        {{$data['email']}}<br/>
        Payment made: {{$data['createdDate']}} at {{$data['createdTime']}}</p>
    </div>
    <br/>

    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection