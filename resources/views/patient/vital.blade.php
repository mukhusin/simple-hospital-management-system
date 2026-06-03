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

			<div class="form-inline">
				<div class="form-group">
					<label>Temperature</label>
					<input type="text" class="form-control" name="temperature" value="{{$patient->vitalSign->temperature}}" placeholder="Enter Temperature Readings">
				</div>
				<div class="form-group">
					<label>Height</label>
					<input type="text" class="form-control" name="height" value="{{$patient->vitalSign->height}}" placeholder="Enter Height Readings">
				</div>
			</div>

			<div class="form-inline">
				<div class="form-group">
					<label>Weight</label>
					<input type="text" class="form-control" name="weight" value="{{$patient->vitalSign->weight}}" placeholder="Enter Weight Readings">
				</div>
				<div class="form-group">
					<label>Blood Presure (BP)</label>
					<input type="text" class="form-control" name="blood" value="{{$patient->vitalSign->blood}}" placeholder="Enter BP readings">
				</div>
			</div>

			<hr>

			<input type="submit" name="button" class="btn btn-success" value="Save Changes">

		</form>

	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('form').on("keyup keypress", function(e) {
		    var code = e.keyCode || e.which; 
		    if (code  == 13) {               
		        e.preventDefault();
		        return false;
		    }
		});
	});
</script>

@endsection