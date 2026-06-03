@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label>Ward Name</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Ward Name" name="name" value="{{ $ward->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Price per Hour</label>
					<div>
						<input type="number" class="form-control" placeholder="Enter Per Hour Price" name="price" value="{{ $ward->price ?? '' }}" required>
					</div>
				</div>
				
			</div>

			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			
		</div>
	</div>
	</form>
@show