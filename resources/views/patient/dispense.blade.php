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
		
			<table class="table">
				<thead>
					<tr>
						<th>Medicine</th>
						<th>Dosage</th>
						<th>Unit Price</th>
						<th>Quantity based on dosage</th>
						<th>Total Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 0;
					?>
					@foreach ($attend->medicals as $m)
						@if ($m->paid == 0 && ($m->checker_id == NULL || $m->checker_id == '' ) && $m->emergence == 0 )
							{{'';++$i; $med = Medical::find($m->medical_id)}}
							<tr>
								<td>
									{{$med->brand}} - {{$med->name}}
								</td>
								<td>
									{{$m->dosage}}
								</td>
								<td>
									<input type="number" name="price_{{$m->medical_id}}" value="{{$med->price($attend->id)}}" readonly="readonly" class="form-control price">
								</td>
								<td>
									<input type="number" name="{{$m->medical_id}}"  placeholder="1" class="form-control qnt" required>
								</td>
								<td>
									<input type="number" name="price_{{$m->medical_id}}" value="{{$med->price($attend->id)}}" readonly="readonly" class="form-control sum">
								</td>
							</tr>
						@endif
					@endforeach
					
				</tbody>
			</table>
			
			<hr>

			<table width="100%">
				<tr>
					<th>Total Bill Medicine: </th>
					<th><input id="Tsum" type="text" value="0" readonly="readonly" class="form-control autoNumber"></th>
				</tr>
			</table>		

			<hr>
			@if ($i > 0)
				<input type="submit" name="button" class="btn btn-success" value="I have confirm the bill with client, checkout">
			@else 
				<input type="submit" name="button" class="btn btn-success" value="Seen">
			@endif
			
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

		$('.price').change(function(event) {
			reCalPro();
		});
		$('.qnt').change(function(event) {
			reCalPro();
		});


	});

	jQuery(function($) {
	    $('.autoNumber').autoNumeric('init');
	});

	function reCalPro() {
		var sum = 0;
		$.each($('.price'), function(i, val) {
			var q = Number($('.qnt')[i].value);
			var tc = Number($('.price')[i].value)*q;
			$('.sum')[i].value = tc;
			sum = sum + tc;
		});
		debugger
		$('#Tsum').val(sum);
	}

</script>

@endsection