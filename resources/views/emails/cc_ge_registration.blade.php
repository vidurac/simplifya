@extends('layout.email')
@section('content')
    @if($data['entity_name'] == "Compliance Company")
        <strong> New CC registration</strong>
    @else
        <strong> New GE registration</strong>
    @endif
    <br/>
    <div>
        @if($data['entity_name'] == "Compliance Company")
            {{$data['company_name']}} <br/>
        @else
            {{$data['company_name']}} <br/>
        @endif
        {{$data['entity_name']}}<br/>
        {{$data['registrant']}}
    </div>
    <br/>

    <p>To approve or reject this company, log into your Simplifya Admin account and change the company's status on your dashboard. </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection