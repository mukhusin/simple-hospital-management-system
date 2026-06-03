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
			
			<h3>Patients Payment Summary</h3>
			<div class="col-lg-12 well">
				<table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata1">
					<thead>
						<tr>
							<th>Time</th>
							<th>Patient</th>
							<th>Gender</th>
							<th>Cashier</th>
							<th>Payment Type</th>
							<td align="right" class="strong">Tendered Amount</td>
							<td align="right" class="strong">Total Bill</td>
							<td align="right" class="strong">Cash Payment</td>
							<td align="right" class="strong">Ratio</td>
						</tr>
					</thead>
					<?php
					$t_bill = $t_pay = $t_tender = 0;
					?>
					<tbody>
						@foreach ($data1 as $key => $value)
							@if ($value->insurance_id > 0)
								<?php
								$type = Insurance::find($value->insurance_id)->name;
								$tendered = 0;
								$paid = 0;
								?>
							@else 
								<?php
								$type = "Cash";
								$tendered = $value->tendered;
								$paid = $value->paid;
								?>
							@endif
							<tr>
								<td>{{date('H:i',strtotime($value->created_at))}}</td>
								<td>{{$value->patient}}</td>
								<td>{{$value->gender}}</td>
								<td>{{($value->creator)}}</td>
								<td>{{($type)}}</td>
								<td align="right" >{{number_format($tendered)}}</td>
								<td align="right" >{{number_format($value->bill)}}</td>
								<td align="right" >{{number_format($paid)}}</td>
								<td align="right" >
									@if ($value->bill > 0)
										{{round($paid/$value->bill,2)}}
									@else
										-
									@endif
								</td>
							</tr>
							<?php 
								$t_bill += $value->bill;
								$t_pay += $paid;
								$t_tender += $tendered;
								$r_r = 0;

								if ($t_bill > 0) {
									$r_r = round($t_pay/$t_bill,2);
								}


							?>
						@endforeach
							<tr>
								<td colspan="5">Total Summation</td>
								<td align="right">{{number_format($t_tender)}}</td>
								<td align="right">{{number_format($t_bill)}}</td>
								<td align="right">{{number_format($t_pay)}}</td>
								<td align="right">{{$r_r}}</td>
							</tr>
				    </tbody>
				</table>
			</div>
			
			
			<h3>Patients Bills Summary</h3>
			<div class="col-lg-12 well">
				<table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata">
					<thead>
						<tr>
							<th>Time</th>
							<th>Patient</th>
							<th>Gender</th>
							<th>Category</th>
							<th>Bill</th>
							<th>Bill Description</th>
							<th>Cashier</th>
							<td align="right" class="strong">Bill Amount</td>
						</tr>
					</thead>
					<tbody>
						@foreach ($data2 as $key => $value)
							<tr>
								<td>{{date('H:i',strtotime($value->created_at))}}</td>
								<td>{{$value->patient}}</td>
								<td>{{$value->gender}}</td>
								<td>{{$value->group	}}</td>
								<td>{{$value->name}}</td>
								<td>{{$value->dosage}}</td>
								<td>{{User::find($value->creator_id)->name}}</td>
								<td align="right">{{number_format($value->amount)}}</td>
							</tr>
						@endforeach
				    </tbody>
				</table>
				
			</div>

			<link rel="stylesheet" href="{{ asset('css/dataTables.tableTools.css') }}"> 
			<link rel="stylesheet" href="{{ asset('css/datatable.css') }}"> 
    		
            <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    		<script src="{{ asset('js/dataTables.tableTools.js') }}"></script>
    		
            <script type="text/javascript" >
               
          //       Table0 = $('#tabledata1').DataTable({
		        //     dom: 'T<"clear">lfrtip',
		        //     deferRender: true,
		        //     "tableTools": {
		        //             "aButtons": [
		        //                 {
		        //                     "sExtends": "csv",
		        //                     "sButtonText": "Save to CSV / Excel"
		        //                 },
		        //             ]
		        //         },
		        //     "aaSorting": [],
		        //     "sPaginationType": "full_numbers",
		        // }); 


		        // Table1 = $('#tabledata').DataTable({
		        //     dom: 'T<"clear">lfrtip',
		        //     deferRender: true,
		        //     "tableTools": {
		        //             "aButtons": [
		        //                 {
		        //                     "sExtends": "csv",
		        //                     "sButtonText": "Save to CSV / Excel"
		        //                 },
		        //             ]
		        //         },
		        //     "aaSorting": [],
		        //     "sPaginationType": "full_numbers",
		        // }); 
            </script>
			
		</div>
	</div>
@endsection