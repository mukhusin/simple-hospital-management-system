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

    		<div class="col-lg-12 well	">
    			<h3>Get Laboratory Report <hr> </h3>
    			<form method="POST" action="{{ url()->current() }}">
@csrf
		    	<div class="form-inline">

			    	<div class="form-group float1">
			    		<label>Select Date</label>
			    		<input type="date" name="from" class="form-control">
			    	</div>

					<div class="form-group float1">
						<label>Select Date</label>
						<input type="date" name="to" class="form-control">
					</div>

					<div class="form-group float1">
						<label> Type </label>
						{{ Form::select( 'type', ['view'=>'view','download'=>'download'],'' ,['class'=>'width_auto '] ) }}
					</div>

			    	<div class="form-group">
			    		<input type="submit" class="btn btn-primary" name="button" value="Get a Report Now!">
			    	</div>
		    		
		    	</div>

		    	</form>

    		</div>	

    		&nbsp;
    		<hr>

			<div class="col-lg-12 well">
    			<h3>Laboratory Test Trend <hr> </h3>

				<table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata">
					<thead>
						<tr>
							<th>Date and Time</th>
							<th>Lab Test</th>
							<th>Patient</th>
							<th>Gender</th>
							<th>Doctor</th>
							<th>Technician</th>
							<th>Results</th>
							<th>Attachment</th>
						</tr>
					</thead>
					<tbody>
						
				    </tbody>
				</table>
			</div>
    	</div>
	</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    $('#tabledata').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url("report/lab/get") }}',
        columns: [
            { data: 'created_at' },
            { data: 'test' },
            { data: 'patient' },
            { data: 'gender' },
            { data: 'doctor' },
            { data: 'technician' },
            { data: 'results_register' },
            { data: 'attachment', orderable: false, searchable: false },
        ],
        order: [],
    });
});
</script>
@endpush