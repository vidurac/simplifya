@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>Unfortunately, your registration on Simplifya for {{ $data['rejected_company_name'] }} could not be approved at this time. Any holds placed upon your payment method have been removed. If you have any questions, please do not hesitate to contact us.</p>
    </p>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection