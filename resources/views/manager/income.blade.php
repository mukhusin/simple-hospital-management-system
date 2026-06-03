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


				<table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata">
					<thead>
						<tr>
							<th>Income Date</th>
							<th>Cons</th>
							<th>Phar</th>
							<th>Lab</th>
							<th>Service</th>
							<th>Total Bill</th>
							<th>Amount Collected</th>
							<th> &nbsp; </th>
						</tr>
					</thead>
					<tbody>
						@foreach ($data as $key => $value)
							<tr>
								<td>{{date('d M Y',strtotime($key))}}</td>
								<td>{{number_format($value['Consultation Fee'])}}</td>
								<td>{{number_format($value['Pharmacy'])}}</td>
								<td>{{number_format($value['Laboratory'])}}</td>
								<td>{{number_format($value['Service Charge'])}}</td>
								<td>{{number_format($value['Total Bill'])}}</td>
								<td>{{number_format($value['Amount Paid'])}}</td>
								<td><a href="{{url('report/income/date/'.$key)}}" title="Detail Report">More</a></td>
							</tr>
						@endforeach
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