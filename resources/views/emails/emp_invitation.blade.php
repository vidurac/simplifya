@extends('layout.email')
@section('content')
    <p>Hi {{$name}},</p>
    <p>You’ve been invited to join the Simplifya account for {{$data['company_name']}}.
        Simplifya is the best software for ensuring cannabis businesses are compliant with the industry’s ever-changing laws.</p>
    <p>{{$data['sent_name']}} has invited you to join. To accept your invitation, please click the following link:</p>
    <p>
        <a style="border-radius:3px;color:white;font-size:15px;padding:14px 7px 14px 7px;max-width:210px;font-family:proxima_nova,'Open Sans','lucida grande','Segoe UI',arial,verdana,'lucida sans unicode',tahoma,sans-serif;border:1px #1373b5 solid;text-align:center;text-decoration:none;width:210px;margin:6px auto;display:block;background-color:#007ee6" href="{{$data['url']}}" target="_blank">Click here</a>
    </p>
    <br>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection