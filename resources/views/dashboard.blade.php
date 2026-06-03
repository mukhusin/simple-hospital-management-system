@extends('master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
@endpush

@section('main')

    <div class="col-lg-12">
        @if (session('error'))
            <div class="error-div">
                {{session('error')}}
            </div>
        @endif

        @if (session('success'))
            <div class="success-div">
                {{session('success')}}
            </div>
        @endif
    </div>

    <?php

    $Mon = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");


    ?>
    

    @if (Auth::user()->role_id != 6 && Auth::user()->role_id != 4)
    	<div class="col-lg-12 widget1">

            <sub_title>Patients Queue</sub_title>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata2">
                <thead>
                    <tr>
                        <th>At Queue</th>
                        <th>From</th>
                        <th>Patient</th>
                        <th>Gender</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    @endif


    @if (Auth::user()->role_id == 6)
        <div class="col-lg-12" style="overflow:auto">
            <div class="box1">
                <hd>Medicine Count</hd>
                <bd>{{Medical::count()}}</bd>
            </div>

            <div class="box1">
                <hd>Lab Test Count</hd>
                <bd>{{\App\Models\LabTest::count()}}</bd>
            </div>

            <div class="box1">
                <hd>Diagnosi  Disease</hd>
                <bd>{{Diagnosis::count()}}</bd>
            </div>

            <div class="box1">
                <hd>Patients  Files</hd>
                <bd>{{Patient::count()}}</bd>
            </div>

        </div>

        <div class="col-lg-12">

            <hr>

            <div class="col-lg-12 widget1">

                <sub_title>Live Patients Attendance </sub_title>
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata2">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Gender</th>
                            <th>Current at</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <br> &nbsp; <br>
            <hr>&nbsp;

            <h3>Number of Patients Attendanded</h3>
             <div class="graph">
                <div id="tp"></div>
            </div>
            <script type="text/javascript">
                Morris.Bar({
                element: 'tp',
                data: [
                    @foreach ($total as $k => $v)
                        <?php
                        $i = $j = 0;
                        if (isset($v[0])) {
                            $i = $v[0];
                        }
                        if (isset($v[1])) {
                            $j = $v[1];
                        }
                        ?>
                        {x: '{{$k}}', y: {{$i}}, z: {{$j}}},
                    @endforeach
                ],
                xkey: 'x',
                ykeys: ['y', 'z'],
                labels: ['New Attendanded','Re Attendanded']
                }).on('click', function(i, row){
                    console.log(i, row);
                });
            </script>
                

            <hr>
            <h3> Revenue </h3>
            <div class="graph">
                <div id="rev"></div>
            </div>
            <script type="text/javascript">
                Morris.Bar({
                element: 'rev',
                data: [
                    @foreach ($revenue as $i)
                        {x: '{{$Mon[$i->m].' - '.$i->y}}', y: {{$i->sum}}},
                    @endforeach
                ],
                xkey: 'x',
                ykeys: ['y'],
                labels: ['revenue per month']
                });
            </script>

            
        </div>
    @elseif(Auth::user()->role_id == 4)

        <div class="col-lg-12 widget1">
        <sub_title>Patients Sample Queue </sub_title>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata3">
                <thead>
                    <tr>
                        <th>At Queue</th>
                        <th>Labolatory Test</th>
                        <th>Requestee</th>
                        <th>Patient</th>
                        <th>Gender</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data1 as $d)
                        <tr>
                            <td>{{$d->time}}</td>
                            <td>{{$d->test}}</td>
                            <td>{{$d->from_user}}</td>
                            <td>{{$d->patient}}</td>
                            <td>{{$d->gender}}</td>
                            <td>
                                <a class="opt" href="{{url('sample/'.$d->a_id.'/'.$d->t_id)}}"> + S </a>
                            </td>
                        </tr>
                    @endforeach         
                </tbody>
            </table>
        </div>

        <br>
        <hr> &nbsp; <hr>
        <br>

        <div class="col-lg-12 widget1">
            <sub_title>Patients Test Queue</sub_title>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata2">
                <thead>
                    <tr>
                        <th>At Queue</th>
                        <th>Labolatory Test</th>
                        <th>Sample</th>
                        <th>Requestee</th>
                        <th>Patient</th>
                        <th>Gender</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data2 as $d)
                        <tr>
                            <td>{{$d->time}}</td>
                            <td>{{$d->test}}</td>
                            <td>{{$d->al->sample_register}}</td>
                            <td>{{$d->from_user}}</td>
                            <td>{{$d->patient}}</td>
                            <td>{{$d->gender}}</td>
                            <td>
                                <a class="opt" href="{{url('result/'.$d->al->id)}}"> + R </a>
                            </td>
                        </tr>
                    @endforeach         
                </tbody>
            </table>
        </div>
    @endif


    <br>
    <hr> &nbsp; <hr>

    <?php $a = Auth::user()->role_id; ?>
    @if ( $a == 3 || $a == 6)

        <div class="col-lg-6 well">
            <h3>Labolatory Services</h3>
            <div class="scroll">
                <table class="table well col-lg-12 pull-left" width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="lab_1">
                    <thead>
                        <tr>
                            <th>Labolatory Test Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $l = new \App\Models\LabTest();
                            $l = $l->orderBy('uom')->get();
                         ?>
                        @foreach ($l as $d)
                            <tr>
                                <td>{{$d->uom}} - {{$d->name}}</td>
                                <td>{{number_format($d->price)}}</td>
                            </tr>
                        @endforeach         
                    </tbody>
                </table>

                
            </div>
        </div>
        
    @endif
    

    <div class="col-lg-6 well">
        <h3>Staff in Session</h3>
        <div class="scroll">
            <table class="table well col-lg-12 pull-left" width="100%" id="insession_1">
                <thead>
                    <tr>
                        <th>Office </th>
                        <th>Staff Name</th>
                        <th>Last Checkin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\InSession::getAll() as $i)
                        <tr>
                            <td>{{$i->office->name}}</td>
                            @if ($i->user)
                                <td>{{$i->user->name}}</td>
                            @else
                                <td> - </td>
                            @endif
                            <td> {{date('H:i D',strtotime($i->updated_at))}} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>


</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    @if (Auth::user()->role_id != 6 && Auth::user()->role_id != 4)
    // Patients Queue — server-side via Yajra
    $('#tabledata2').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url("widget/get/inoffice") }}',
        columns: [
            { data: 'updated_at' },
            { data: 'from_office' },
            { data: 'patient' },
            { data: 'gender' },
            { data: 'options', orderable: false, searchable: false },
        ],
        order: [],
    });
    @endif

    @if (Auth::user()->role_id == 6)
    // Live Patients Attendance — server-side via Yajra
    $('#tabledata2').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url("widget/get/inoffices") }}',
        columns: [
            { data: 'patient_id' },
            { data: 'patient' },
            { data: 'gender' },
            { data: 'to_office' },
            { data: 'options', orderable: false, searchable: false },
        ],
        order: [],
    });
    @endif

    @if (Auth::user()->role_id == 4)
    // Lab Sample & Test queues — client-side
    $('#tabledata3').DataTable({ order: [], pageLength: 25 });
    $('#tabledata2').DataTable({ order: [], pageLength: 25 });
    @endif

    @if (Auth::user()->role_id == 3 || Auth::user()->role_id == 6)
    // Lab services list — client-side
    $('#lab_1').DataTable({ order: [], pageLength: 25 });
    @endif

    // Staff in Session — client-side
    $('#insession_1').DataTable({ order: [], pageLength: 25 });

});
</script>
@endpush