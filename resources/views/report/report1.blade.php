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
	    		<label>Report Date</label>
	    		<input type="date" name="date" class="form-control">
	    		<hr>
	    		<input type="submit" class="btn btn-primary" name="button" value="Print Report">
	    	</div>
    		
    	</div>

    	</form>
    </div>

</div>

@endsection