@extends('layout.email')
@section('content')
    <strong>NEW BUSINESS REGISTRATION</strong>

    <p>{{$data['company_name']}}</p>
    <p>{{$data['registrant']}}</p>
    <?php if ($data['registrantEmail']): ?>
        <p>{{$data['registrantEmail']}}</p>
    <?php endif;?>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection

