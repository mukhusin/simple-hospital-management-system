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

			<?php
			$tes = $attend->test;
			$tes = explode(',', $tes);
			?>

			<h3>Investigation Work <hr></h3>

			@foreach ($tes as $t)
				<div class="form-group">
					<label>{{LabTest::find($t)->code}} : {{LabTest::find($t)->name}}</label>
					<input type="text" name="{{$t}}_results" placeholder="Enter Results" class="form-control" style="width:50%">
				</div>
				<div class="form-group">
					<label>{{LabTest::find($t)->name}} Test Remark</label>
					<textarea name="{{$t}}_remark" class="text_editor" placeholder="Enter Remark"></textarea>
				</div>
				<hr>
			@endforeach

			<!-- <hr> -->

			<input type="submit" name="button" class="btn btn-success" value="Save Changes">

			</form>

	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {

	});
</script>

@endsection