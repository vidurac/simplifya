@extends('layout.email')
@section('content')
    <strong> Welcome to Simplifya!</strong> <br/>
    <p>Thank you for registering your business on Simplifya. We currently have an approval process for compliance companies and government entities that register on Simplifya. A Simplifya representative will contact you at the contact information you provided during registration.</p>
    {{--<p>Hello {{$data['name']}},</p>--}}
    {{--<p> Thank you for registering at {{$data['company']}}.--}}
    {{--There will be a company approval process and you have to wait till we approve your company in order to use the system.--}}
    {{--</p>--}}
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} Team</p>
@endsection

