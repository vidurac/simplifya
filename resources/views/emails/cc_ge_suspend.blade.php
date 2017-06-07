@extends('layout.email')
@section('content')
    <p>Hello {{$name}},</p>
    <p>
        The account for {{$data['businessName']}} has been suspended.
        If you have questions or believe this to be an error, please contact us at
        <a href="mailto:contact@simplifya.com">contact@simplifya.com</a> or (877) 464-8398.
    </p>

    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection