@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label>FullName</label>
					<div>
						{{$bill->attendance->patient->name}}
					</div>
				</div>
				<div class="form-group">
					<label>Gender</label>
					<div>
						{{$bill->attendance->patient->gender}}
					</div>
				</div>
			</div>
			<div class="form-inline" >
				<div class="form-group">
					<label>Name</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ $bill->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Price</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Amount" name="amount" value="{{ $bill->amount ?? '' }}" required>
					</div>
				</div>
			</div>
			<div class="form-inline" >
				<div class="form-group">
					<label>Description</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Desciption" name="dosage" value="{{ $bill->dosage ?? '' }}" required>
					</div>
				</div>
				
			</div>

			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show