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

			<!-- <hr> -->
		</div>
	</div>

	<div class="row">
        <div class="col-lg-12">

            <div class="graph1">
                <div id="gra" style="width: 50%; height: 500px; float: left"></div>
                <div class="" id="piechart" style="width: 50%; height: 500px; float: left"></div>

            </div>
            <script type="text/javascript">
                Morris.Donut({
                element: 'gra',
                data: [
                    @foreach($transactions1 as $i =>  $transaction)
                        {label: '{{$transaction->name}}', value: {{$transaction->total}} },
                    @endforeach
                ],
                });
            </script>




            <!-- <hr> -->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <h3> Aggregated Expense By Category </h3>
            <table class="table dataTable well" title="{{$title}}">
                <thead>
                    <tr>
                        <th> Expense Category </th>
                        <th width="150" class="text-right"> Amount </th>
                        <th width="150" class="text-right"> Total </th>
                    </tr>
                </thead>
                <tbody class="table-column">
                    <?php $sum1 = 0; ?>
                    @foreach($transactions1 as  $transaction)
                        <?php $sum1 += $transaction->total ?>
                        <tr>
                            <td> {{ $transaction->name }} </td>
                            <td class="text-right"> {{ number_format($transaction->total) }} </td>
                            <td class="text-right"> {{ number_format($sum1) }} </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td> Total Expense </td>
                        <td></td>
                        <td class="text-right"> {{ number_format($sum1) }} </td>
                    </tr>
                </tbody>
            </table>

            <br/> <br/>
            <hr style="width:100%"/>

            <h3> Expense Break Down </h3>
            <table class="table dataTable well" title="{{$title}}">
                <thead>
                    <tr>
                        <th> Date </th>
                        <th> Remark </th>
                        <th class="text-right"> Amount </th>
                        <th class="text-right"> Total </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sum1 = 0; ?>
                    @foreach($transactions as  $transaction)
                        <?php $sum1 += $transaction->amount ?>
                        <tr>
                            <td> {{ $transaction->created_at->format('H:i d M Y') }} </td>
                            <td> {{ $transaction->expense->name }} - {{ $transaction->remark }} </td>
                            <td class="text-right"> {{ number_format($transaction->amount) }} </td>
                            <td class="text-right"> {{ number_format($sum1) }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

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