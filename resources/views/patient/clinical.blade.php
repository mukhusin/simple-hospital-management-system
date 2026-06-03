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
				<label>Complain</label>
				<textarea name="complain" class="text_editor">{{$attend->complain}}</textarea>
			</div>

			<div class="form-group">
				<label>General Examination</label>
				<textarea name="general_observation" class="text_editor">{{$attend->general_observation}}</textarea>
			</div>


			<div class="form-group">
				<label>Systemic Examination</label>
				<textarea name="systemic_observation" class="text_editor">{{$attend->systemic_observation}}</textarea>
			</div>

			<div class="form-group">
				<label>Review of other system</label>
				<textarea name="review" class="text_editor">{{$attend->review}}</textarea>
			</div>

			

			<div class="form-group">
				<label>Provision Diasnosis</label>

				<select class="chosen-select-width" multiple="multiple" name="provision[]" id="provision" data-placeholder="Select Provision Diasnosis " >
				    @foreach(Widget::diagnosis_list() as $k => $v)
				        <option value="{{$k}}"
				        	@foreach(explode(',',$attend->provision) as $p) 
				        		@if( $p == $k)
				        			selected="selected"
				        		@endif 
				        	@endforeach>
				        	{{$v}}
				        </option>
				    @endforeach
				</select>

			</div>
			<div class="form-group">
				<label>Differential Diasnosis</label>
				<select class="chosen-select-width" multiple="multiple" name="differential[]" id="differential" data-placeholder="Select Differential Diasnosis " >
				    @foreach(Widget::diagnosis_list() as $k => $v)
				        <option value="{{$k}}"
				        	@foreach(explode(',',$attend->differential) as $p) 
				        		@if( $p == $k)
				        			selected="selected"
				        		@endif 
				        	@endforeach>
				        	{{$v}}
				        </option>
				    @endforeach
				</select>

			</div>
			<div class="form-group">
				<label>Final Diasnosis</label>

				<select class="chosen-select-width" multiple="multiple" name="final[]" id="final" data-placeholder="Select Final Diasnosis " >
				    @foreach(Widget::diagnosis_list() as $k => $v)
				        <option value="{{$k}}"
				        	@foreach(explode(',',$attend->final) as $p) 
				        		@if( $p == $k)
				        			selected="selected"
				        		@endif 
				        	@endforeach>
				        	{{$v}}
				        </option>
				    @endforeach
				</select>
				
			</div>
			
			<hr>

			<input type="submit" name="button" class="btn btn-success" value="Save Changes">

			</form>

	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {
		// $("#provision").val([{{$attend->provision}}]).select2({
		// 	// multiple: true,
		// 	placeholder: "Select Diasgnos Code",
		// });
		// $("#differential").val([{{$attend->differential}}]).select2({
		// 	// multiple: true,
		// 	placeholder: "Select Diasgnos Code",
		// });
		// $("#final").val([{{$attend->final}}]).select2({
		// 	// multiple: true,
		// 	placeholder: "Select Diasgnos Code",
		// });
	});
</script>

@endsection