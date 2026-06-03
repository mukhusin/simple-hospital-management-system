@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label>Name</label>
					<div>
						{{$medical->name}}
					</div>
				</div>
				<div class="form-group">
					<label>Brand</label>
					<div>
						{{$medical->brand}}
					</div>
				</div>
				<div class="form-group">
					<label>Unit</label>
					<div>
						{{$medical->unit}}
					</div>
				</div>
				<div class="form-group">
					<label>Price per Unit</label>
					<div>
						{{$medical->price_unit}}
					</div>
				</div>
				<div class="form-group">
					<label>Stock Count</label>
					<div>
						{{$medical->getStock()}}
					</div>
				</div>
			</div>

			<hr>

			<link rel="stylesheet" href="{{ asset('css/datatable.css') }}"> 
            <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
            <script type="text/javascript" >
                
                $(document).ready(function() {
                    oTable = $('#tabledata').dataTable({
                        // "bJQueryUI": true,
                        "aaSorting": [],
                        "fnInitComplete": function(oSettings, json) {
                            // auto run scripts
                        },
                        "bServerSide": true,
                        "sPaginationType": "full_numbers",
                        "bProcessing": true,
                        "sAjaxSource": "{{ (isset($source)) ? $source : url('medicines/stock/get/'.$medical->id)}}"
                    });
                } );
            </script>

			<table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata" class="table">
				<thead>
					<tr>
					    <th> Date</th>
						<th> Stock Count </th>
						<th> Previous </th>
						<th> Stock Changes </th>
						<th> Remark</th>
						<th> Staff Associated</th>
					</tr>
				</thead>
				<tbody>

			    </tbody>
			</table>

		</div>
	</div>
	</form>
@show