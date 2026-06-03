@extends('master')

@section('main')
	<div class="row">
		<div class="col-lg-12">

			{{ $text_string ?? '' }}

            {{ Form::open(['method'=>'get']) }}

            <div class="float1">
                <label> From </label> {{ Form::input( 'date','from', $from ,['class'=>'width_auto datepicker'] ) }}
            </div>
            <div class="float1">
                <label> To </label> {{ Form::input( 'date','to', $to ,['class'=>'width_auto datepicker'] ) }}
            </div>
            <div class="float1">
                <button class="btn btn-warning" type="submit"> <i class="fa fa-search"></i> Get Report </button>
            </div>

            {{ Form::close(); }}

            <br/> <br/>

			<!-- <hr> -->
		</div>
	</div>

    <div class="row">
        <div class="col-lg-12">

             @foreach($insurances as $i => $insurance)
                <h3> {{ $insurance->name }} </h3>


                <div class="form-group1">
                    <label>Total Patient Attended</label>
                    <div>
                        {{ number_format(count($insurance->payments))}}
                    </div>
                </div>

                <hr width="100%">

                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th> Date </th>
                            <th> Member ID </th>
                            <th> Patient </th>
                            <th width="150" class="text-right"> Cons </th>
                            <th width="150" class="text-right"> Phar </th>
                            <th width="150" class="text-right"> Lab </th>
                            <th width="150" class="text-right"> Proc </th>
                            <th width="150" class="text-right"> Amount </th>
                            <th width="150" class="text-right"> Total </th>
                        </tr>
                    </thead>
                    <tbody class="table-column">
                        <?php
                        $sum = 0;
                        ?>
                        @foreach($insurance->payments as $payment)
                            <?php $sum += $payment->paid; ?>
                            <tr>
                                <td> {{ date('H:i d M Y', strtotime($payment->created_at)) }} </td>
                                <td> {{ PatientAttendance::find($payment->attendance_id)->insurance_number }} </td>
                                <td> {{ $payment->patient }} </td>
                                <td class="text-right"> {{ number_format( PatientAttendance::sumGroup('Consultation Fee',$payment->attendance_id) ) }} </td>
                                <td class="text-right"> {{ number_format( PatientAttendance::sumGroup('Pharmacy',$payment->attendance_id) ) }} </td>
                                <td class="text-right"> {{ number_format( PatientAttendance::sumGroup('Laboratory',$payment->attendance_id) ) }} </td>
                                <td class="text-right"> {{ number_format( PatientAttendance::sumGroup('Service Charge',$payment->attendance_id) ) }} </td>
                                <td class="text-right"> {{ number_format($payment->bill) }} </td>
                                <td class="text-right"> {{ number_format($sum) }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td> Outstanding Balance </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"> <strong>{{ number_format($sum) }}</strong> </td>
                        </tr>
                    </tfoot>
                </table>



                <script>

                </script>

                <br/>
                <hr style="width:100%"/>

            @endforeach

        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/dataTables.tableTools.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">

    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.tableTools.js') }}"></script>

    <script type="text/javascript" >

        Table0 = $('.dataTable').DataTable({
            dom: 'T<"clear">lfrtip',
            deferRender: true,
            "tableTools": {
                    "aButtons": [
                        {
                            "sExtends": "csv",
                            "sButtonText": "Save to CSV / Excel"
                        },
                    ]
                },
            "aaSorting": [],
            "sPaginationType": "full_numbers",
        });
    </script>
@show