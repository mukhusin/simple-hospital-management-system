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

		<form method="POST" action="{{ url()->current() }}">
@csrf

			<h3>{{$test->uom}} - {{$test->name}} <hr></h3>

			<div class="form-inline">
				<div class="form-group">
					<label>Patient</label>
					<div>
						{{$patient->name}}
					</div>
				</div>
				<div class="form-group">
				<label>Gender</label>
					<div>
						{{$patient->gender}}
					</div>
				</div>
			</div>
			<div class="form-inline">
				<div class="form-group">
					<label>Birthday</label>
					<div>
						{{date('D, d M Y',strtotime($patient->dob))}}
					</div>
				</div>
				<div class="form-group">
				<label>Primary Phone Number</label>
					<div>
						{{$patient->phone1}}
					</div>
				</div>
			</div>

			<hr>

			<div class="form-inline">
				<div class="form-group">
					<label>Sample Tag Name</label>
					<div>
						{{$result->sample_register}}
					</div>
				</div>
				<div class="form-group">
				<label>Sample Taker</label>
					<div>
						{{User::find($result->sample_register_id)->name}}
					</div>
				</div>
			</div>

			<hr>

			<div class="form-inline">
				<div class="form-group">
					<label>Test Results</label>
					<div>
						<input type="text" name="result" placeholder="Enter Test Results" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
				<label>Test Results Attchment</label>
					<div>
						<input type="file" class="form-control" name="attachment" placeholder="Select Attachment">
					</div>
				</div>
			</div>

			<hr>

			<div class="form-inline">
				<div class="form-group">
					<input type="submit" name="button" class="btn btn-block btn-success" value="Save Changes">
				</div>
			</div>

			<!-- <input type="submit" name="button" class="btn btn-success" value="Save Changes"> -->

			</form>

	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {

	});
</script>

@endsection