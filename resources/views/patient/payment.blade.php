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
				
			<div class="panel panel-default">
			    <div class="panel-heading">
			    	{{$patient->name}} Bill
			    </div>
			    <div class="panel-body">
					<table class="table">
						<thead>
							<tr>
								<th>Bill Description</th>
								<th style="float:right">Bill Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$ab = new AttendanceBill();
				  			$ab = $ab->where('attendance_id','=',$attend->id);
				  			$ab = $ab->where('status','=','unpaid');
				  			$ab = $ab->get();
				  			$sum = 0;
							?>
							@foreach ($ab as $b)
								{{'';$sum+=$b->amount}}
								<tr>
									<td>
										{{$b->name}}
									</td>
									<td align="right">
										{{number_format($b->amount)}}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
			    	
			    </div>
			</div>

			<div class="well">
				
				<h3>Total Amount is {{number_format($sum)}} Tshs</h3>

				<?php
				$l = strlen(number_format($sum));
				?>

				@if ($attend->insurance_id > 0)
					
					<hr>
					<input type="submit" name="button" class="btn btn-success" value="Print Invoice and Checkout">
					
				@else
					<hr>
					<input id="paid" type="text" name="paid" class="autoNumber form-control"  data-v-max="{{$sum*10}}.00" data-v-min="0" pattern=".{<?php echo $l-1 ?>,<?php echo $l+1?>}"  placeholder="Enter Tendered Amount" required>

					<hr>
					<input type="submit" name="button" class="btn btn-success" value="Submit Payment and Checkout">
				@endif
			</div>



			</form>
			
	</div>

</div>

<script type="text/javascript">
	jQuery(function($) {
	    $('.autoNumber').autoNumeric('init');
	});
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