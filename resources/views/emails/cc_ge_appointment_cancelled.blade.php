@extends('layout.email')
@section('content')
    <strong> Audit Cancellation Notice </strong>
    <p>The following audit has been cancelled:</p>
    <p>
        {{$data['to_company']}}<br/>
        {{$data['location_name']}}
        <br>
        <?php echo trim($data['address_line_1']);?>{{($data['address_line_2'] !='')?', '.$data['address_line_2'].'.':'.'}}
        <br/>
        {{$data['city']}}, {{$data['state']}} {{($data['zip_code'] != '')?$data['zip_code']:''}}
    </p>
    <p>Date/Time: {{$data['inspection_Date']}}  at {{$data['inspection_Time']}}</p>
    @if ($data['comment'] && $data['comment'] != '')
        <p>Comments : {{$data['comment']}}</p>
    @endif
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} Team</p>
@endsection