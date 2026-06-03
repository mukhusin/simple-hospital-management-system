@extends('master')

@section('main')
	<div class="row">
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
    	<div class="col-lg-12">
			<div class="col-lg-12 well">

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


				<table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata">
					<thead>
						<tr>
							<th>Date</th>
							<th class="text-right"> Cons</th>
							<th class="text-right"> Phar</th>
							<th class="text-right"> Lab</th>
							<th class="text-right"> Service</th>
							<th class="text-right"> Total Bill</th>
							<th class="text-right">  Cash </th>
							<th class="text-right">  Credit </th>
							<th class="text-right">  Forgiven </th>
							<th> &nbsp; </th>
						</tr>
					</thead>
					<tbody>
					    <?php
					    $con = 0;
					    $phar = 0;
					    $lab = 0;
					    $ser = 0;
					    $bil = 0;
					    $cash = 0;
					    $cred = 0;
					    $for = 0;
					    ?>
						@foreach ($data as $key => $value)
						    <?php
                            $con += $value['Consultation Fee'];
                            $phar += $value['Pharmacy'];
                            $lab += $value['Laboratory'];
                            $ser += $value['Service Charge'];
                            $bil += $value['Total Bill'];
                            $cash += $value['Cash'];
                            $cred += $value['Credit'];
                            $for += $value['Total Bill'] - $value['Cash'] - $value['Credit'];
                            ?>
							<tr>
								<td class="text-right">{{date('d M Y',strtotime($key))}}</td>
								<td class="text-right">{{number_format($value['Consultation Fee'])}}</td>
								<td class="text-right">{{number_format($value['Pharmacy'])}}</td>
								<td class="text-right">{{number_format($value['Laboratory'])}}</td>
								<td class="text-right">{{number_format($value['Service Charge'])}}</td>
								<td class="text-right">{{number_format($value['Total Bill'])}}</td>
								<td class="text-right">{{number_format($value['Cash'])}}</td>
								<td class="text-right">{{number_format($value['Credit'])}}</td>
								<td class="text-right">{{number_format( $value['Total Bill'] - $value['Cash'] - $value['Credit'] )}}</td>
								<td><a href="{{url('report/sales_report/date/'.$key)}}" title="Detail Report">More</a></td>
							</tr>
						@endforeach
						<tr>
                            <td class="text-right"> <strong>Total</strong> </td>
                            <td class="text-right">{{number_format($con)}}</td>
                            <td class="text-right">{{number_format($phar)}}</td>
                            <td class="text-right">{{number_format($lab)}}</td>
                            <td class="text-right">{{number_format($ser)}}</td>
                            <td class="text-right">{{number_format($bil)}}</td>
                            <td class="text-right">{{number_format($cash)}}</td>
                            <td class="text-right">{{number_format($cred)}}</td>
                            <td class="text-right">{{number_format( $for )}}</td>
                            <td></td>
                        </tr>
				    </tbody>
				</table>
				
	            <link rel="stylesheet" href="{{ asset('css/dataTables.tableTools.css') }}"> 
				<link rel="stylesheet" href="{{ asset('css/datatable.css') }}"> 
        		
	            <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
        		<script src="{{ asset('js/dataTables.tableTools.js') }}"></script>
        		
	            <script type="text/javascript" >
	               
	                Table0 = $('#tabledata').DataTable({
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
				
			</div>
    	</div>
	</div>
@endsection