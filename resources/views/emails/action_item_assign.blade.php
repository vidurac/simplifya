@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>You have been assigned to an action item.</p>
    <p>Audit #: {{$data['inspection_no']}}</p>
    <p>Action Item : {{$data['action_item']}}</p><br>
    <p>You can respond to this action item by logging into your Simplifya account and going to the corresponding audit report. From there, you can add notes and images to show that the problem has been remedied. You can also log into the Simplifya iOS or Android phone app to respond to this action item.</p><br>
    <p>If you have any questions about this Action Item, please refer to the person that assigned this to you.</p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection
