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
						<input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ $procedure->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Price</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Price" name="price" value="{{ $procedure->price ?? '' }}" required>
					</div>
				</div>
			</div>

			@if (isset($procedure))
				<div class="form-inline" >
					<h3>Insurances Prices</h3>
					@foreach ($procedure->prices() as $p)
						<div class="form-group">
							<label>{{ucwords($p->insurance->name)}}</label>
							<div>
								<input type="hidden" name="insurance_id[]" value="{{$p->id}}">
								<input type="number" class="form-control" placeholder="Enter Price" name="insurance_price[]" value="{{ $p->price ?? '' }}" required>
							</div>
						</div>
						
					@endforeach
				</div>
			@endif



			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show