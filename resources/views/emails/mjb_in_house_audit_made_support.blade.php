@extends('layout.email')
@section('content')
    <strong> New Self-Audit Notification</strong>
    <div>
        <p>{{$data['businessName']}}</p>
        {{--<p>Name - {{$data['name']}}</p>--}}
        {{--<p>Email - {{$data['email']}}</p>--}}
        {{--<p>Business Registration Name- {{$data['reg_no']}}</p> <br/>--}}
        {{--<p>Location: </p>--}}
        <p>
            {{--{{$data['businessName']}}<br/>--}}
            {{$data['location_name']}}
            <br>
            <?php echo trim($data['address_line_1']);?>{{($data['address_line_2'] !='')?', '.$data['address_line_2'].'.':'.'}}
            <br/>
            {{$data['city']}}, {{$data['state']}} {{($data['zip_code'] != '')?$data['zip_code']:''}}
            <br/>
            <?php if (isset($data['contact'])): ?>
                {{$data['contact']}}
            <?php endif; ?>
        </p>

        <p>Audit date : {{$data['inspection_Date']}} <br/>
            Audit time : {{$data['inspection_Time']}}</p>

        @if ($data['inspector_name'] && $data['inspector_email'])
            <p>
                Auditor assigned: {{$data['inspector_name']}}<br/>
                Auditor email: {{$data['inspector_email']}}<br/>
            </p>
        @endif
        <?php if (isset($data['comment']) && $data['comment'] != ''):?>
        <p><b>COMMENTS :</b> {{$data['comment']}}</p>
        <?php endif; ?>

    </div>
    <br/>
    <p>Thanks,<br/>
    The {{$data['company']}} team</p>
@endsection