@extends('layout.email')
@section('content')
    <p>{{ $data['approved_company_name'] }} has been verified and approved to use Simplifya! Please log into the system and complete your company details.</p>
    </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection