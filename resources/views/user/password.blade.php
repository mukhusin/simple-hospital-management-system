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
	<div class="col-lg-8">
     	<form method="POST" action="{{ url()->current() }}">
@csrf

			<div class="form-inline">
          		<div class="form-group" style="min-width:50%" >
					<label>Password</label>
					<div>
					  <input style="width:100%" type="password" class="form-control" placeholder="Enter Password" id="password" name="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_two.pattern = this.value;" required>
					</div>
				</div>

				<div class="form-group"  style="min-width:50%" >
					<label>Re-write Password</label>
					<div>
					  <input style="width:100%" type="password" class="form-control" id="password_two" name="password_two" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');" placeholder="Verify Password" required>
					</div>

			</div>

			<hr/>

			<button type="submit" class="btn btn-primary">Save Record</button>

	</div>
</div>

<script type="text/javascript">
	
	$(document).ready(function() {
		
	});	

</script>

@endsection