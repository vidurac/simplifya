@extends('layout.email')
@section('content')
    Hello,
    <br><br>
    We received a request to reset the password for your account. If you requested a password reset, please click the button below. If you didn’t make this request, please ignore this email.
    <br><br>
    <center>
        <a style="border-radius:3px;color:white;font-size:15px;padding:14px 7px 14px 7px;max-width:210px;font-family:proxima_nova,'Open Sans','lucida grande','Segoe UI',arial,verdana,'lucida sans unicode',tahoma,sans-serif;border:1px #1373b5 solid;text-align:center;text-decoration:none;width:210px;margin:6px auto;display:block;background-color:#007ee6" href="{{ url('verify/password/'.$data['confirmation_code']) }}" target="_blank">Reset Password</a>
    </center>
    <br/>
    <p>Thanks,<br/>
    The {{$data['system']}} Team</p>
@endsection