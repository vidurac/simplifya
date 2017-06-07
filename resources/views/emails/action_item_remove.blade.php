@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>The following action item has been closed.</p>
    <p>Action Item : {{$data['action_item']}}</p>
    <p>To view the action item, click the button below.</p>
    <p><a style="border-radius:3px;color:white;font-size:15px;padding:14px 7px 14px 7px;max-width:240px;font-family:proxima_nova,'Open Sans','lucida grande','Segoe UI',arial,verdana,'lucida sans unicode',tahoma,sans-serif;border:1px #1373b5 solid;text-align:center;text-decoration:none;width:240px;margin:6px auto;display:block;background-color:#007ee6" href="{{$data['url']}}" target="_blank">View Action Item</a></p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
