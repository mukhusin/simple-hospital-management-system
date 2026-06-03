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
		
			<table class="table" id="tables1">
				<thead>
					<tr>
						<th width="50%">Allegies Name</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 0;
					?>
					@foreach (explode(',', $patient->allegies) as $m)
						{{'';++$i}}
						<tr>
							<td>
								<input type="text" name="allegies[]" value="{{$m}}" placeholder="Enter Allegies" class="form-control">
							</td>
						</tr>
					@endforeach
					
					@if ($i == 0)
						@for($i = 0; $i < 3; $i++)
							<tr>
								<td>
									<input type="text" name="allegies[]" placeholder="Enter Allegies" class="form-control">
								</td>
							</tr>
						@endfor
					@endif

				</tbody>
			</table>

			<table class="hidden">
				<?php ++$i ?>
				<tr id="cp">
					<td>
						<input type="text" name="allegies[]" placeholder="Enter Allegies" class="form-control">
					</td>
				</tr>
			</table>

			<hr>
			
			<div class="btn-group">
				<button id="add_row" type="button" class="btn btn-success">Add New Row</button>
				<button id="delete_row" type="button" class="btn btn-danger">Delete Last Row</button>
			</div>

			<hr>

			<input type="submit" name="button" class="btn btn-success" value="Save Changes">

			</form>
			
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {

		$("#delete_row").live('click', function() {
			if ($('#tables1 tr').length >= 3) {
				$('#tables1 tr:last').remove();
			    var $tableBody = $('#tables1').find("tbody");
		        var $trLast = $tableBody.find("tr:last");
				$trLast.find(':text:first').focus();
			};
		});

		$("#add_row").live('click', function() {

			var $tableBody = $('#tables1').find("tbody");
	        var $trLast = $tableBody.find("tr:last");

			var p_id = 'tr_'+Math.round(Math.random()*10000000000000,2);
	        var temp = $('#cp');
	        var temp1 = temp.clone();
	        temp1.attr('id', p_id);
			temp1.find(':text').val('');
		    $trLast.after(temp1);
			temp1.find(':text').focus();

		});

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