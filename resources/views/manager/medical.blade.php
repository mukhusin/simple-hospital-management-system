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
						<input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ $medical->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Brand</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Brand" name="brand" value="{{ $medical->brand ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Unit</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Unit" name="unit" value="{{ $medical->unit ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Price per Unit</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Price" name="price_unit" value="{{ $medical->price_unit ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Stock Count</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Stock" name="stock" value="{{ $medical->stock ?? '' }}" required>
					</div>
				</div>
			</div>

			@if (isset($medical))
				<div class="form-inline" >
					<h3>Insurances Prices</h3>
					@foreach ($medical->prices() as $p)
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