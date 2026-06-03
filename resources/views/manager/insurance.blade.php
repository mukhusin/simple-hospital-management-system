@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label>Name</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ $insurance->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>General Practioner Fee</label>
					<div>
						<input type="number" class="form-control" placeholder="Enter General Practioner Fee" name="consultation_fee" value="{{ $insurance->consultation_fee ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Super Specialist Consultation Fee</label>
					<div>
						<input type="number" class="form-control" placeholder="Enter Super Specialist Consultation Fee" name="specialist_fee" value="{{ $insurance->specialist_fee ?? '' }}" required>
					</div>
				</div>
			</div>

			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show