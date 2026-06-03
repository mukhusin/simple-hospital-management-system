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

			<div class="pane1 btn-info">
				<h3>Medicine</h3>
				<table class="table" id="tables1">
					<thead>
						<tr>
							<th width="50%">Name</th>
							<th width="50%">Dosage</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
						?>
						@foreach ($attend->medicals as $m)
							@if ($m->paid == 0 && ($m->checker_id == NULL || $m->checker_id == '' ) && $m->emergence == 1 )
								{{'';++$i}}
								<tr>
									<td>
										<select class="chosen-select-width" name="med[]" data-placeholder="Select Final Diasnosis " >
										    @foreach(Widget::meds_list() as $k => $v)
										        <option value="{{$k}}"
										        	@foreach(explode(',',$attend->med) as $p) 
										        		@if( $m->medical_id == $k)
										        			selected="selected"
										        		@endif 
										        	@endforeach>
										        	{{$v}}
										        </option>
										    @endforeach
										</select>
									</td>
									<td>
										<input type="text" name="dosage[]" value="{{$m->dosage}}" placeholder="Dosage" class="form-control">
									</td>
								</tr>
							@endif
						@endforeach
						
						@if ($i == 0)
							@for ($i = 0; $i < 3; $i++)
								<tr>
									<td>
										{{Form::select('med[]',
											Widget::meds_list(),
											'',
											array('id' => 'chose_'.$i,'required')
										)}}
										<script type="text/javascript">
											$(document).ready(function() {
												$('#chose_{{$i}}').chosen({width:"99%"});
											});
										</script>
									</td>
									<td>
										<input type="text" name="dosage[]" placeholder="Dosage" class="form-control">
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
							{{Form::select('med[]',
								Widget::meds_list(),
								'',
								array('id' => 'chose_'.$i,'required')
							)}}
						</td>
						<td>
							<input type="text" name="dosage[]" placeholder="Dosage" class="form-control">
						</td>
					</tr>
				</table>

				<hr>
				
				<div class="btn-group">
					<button id="add_row" type="button" class="btn btn-success">Add New Row</button>
					<button id="delete_row" type="button" class="btn btn-danger">Delete Last Row</button>
				</div>
			</div>

			<hr>

			<input type="submit" name="button" class="btn btn-success" value="Submit Information">

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

			var id = 'selec_'+Math.round(Math.random()*10000000000000,2);
			var p_id = 'tr_'+Math.round(Math.random()*10000000000000,2);
	        var temp = $('#cp');
	        var temp1 = temp.clone();
	        temp1.attr('id', p_id);
	        debugger;
			temp1.find(':text').val('');

			temp1.find('select').attr('id', id);
		    $trLast.after(temp1);

			$('#'+id).chosen({width:"99%"});
	        $('#'+id).focus();

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