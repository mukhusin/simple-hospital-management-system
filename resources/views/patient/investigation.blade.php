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
			
			<div class="form-group">
				<label>Labolatory Test</label>
				<select required class="chosen-select-width" multiple="multiple" name="test[]" id="test" data-placeholder="Select Final Diasnosis " >
				    @foreach(Widget::test_list('',$attend->insurance_id) as $k => $v)

			        		@if($attend->canTest($k))
			        			<option value="{{$k}}"
					        	@foreach(explode(',',$attend->test) as $p) 
					        		@if ($k ==  $p)
					        			selected="selected"
					        		@endif
					        	@endforeach
			        				>
						        	{{$v}}
						        </option>
			        		@endif 
				    @endforeach
				</select>
			</div>
			
			<hr>
			
			<input type="submit" name="button" class="btn btn-success" value="Send Patient to Labolatory">
			
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