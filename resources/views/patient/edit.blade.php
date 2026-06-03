@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="well">
				<div class="form-inline" >
					<div class="form-group">
						<label>Full Name</label>
						<div>
							<input type="text" class="form-control" placeholder="Enter Name" pattern="[A-Za-z]+[ ]+[A-Za-z]+[ ]+[A-Za-z]{1,}" name="name" value="{{ $patient->name ?? '' }}" required>
						</div>
					</div>
					<div class="form-group">
						<label>Gender</label>
						<div>
							{{Form::select('gender',
								array(
									'' => 'Select',
									'Male' => 'Male',
									'Female' => 'Female',
								), isset($patient->gender) ? $patient->gender:'', array('class'=>'form-control','required'))}}
						</div>
					</div>
				</div>

				<div class="form-inline" >
					<div class="form-group">
						<label>Primary Phone</label>
						<div>
							<input type="text" class="form-control" name="phone1" value="{{ $patient->phone1 ?? '' }}" pattern="[0-9]{10}" required>
						</div>
					</div>
					<div class="form-group">
						<label>Secondary Phone</label>
						<div>
							<input type="text" class="form-control" name="phone2" value="{{ $patient->phone2 ?? '' }}" pattern="[0-9]{10}" required>
						</div>
					</div>
				</div>

				<div class="form-inline">
					
					<div class="form-group">
						<label>Date of Birth</label>
						<div>
							<input type="date" class="form-control" name="dob" value="{{ $patient->dob ?? '' }}" required>
						</div>
					</div>

					<div class="form-group">
						<label>Address</label>
						<div>
							<input type="text" class="form-control" name="address" value="{{ $patient->address ?? '' }}" required>
						</div>
					</div>
				</div>
				
			</div>
			
			<hr>

			<div class="form-inline well well2" >
				<div class="form-group">
					<label>Insurance Service</label>
					<div>
						{{Form::select('sponsor',
							Widget::getInsurance(), isset($patient->sponsor) ? $patient->sponsor:'', array('class'=>'form-control'))}}
					</div>
				</div>
				<div class="form-group" id="sponsor_code">
					<label>Membership Number</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Membership No" name="sponsor_code" value="{{ $patient->sponsor_code ?? '' }}">
					</div>
				</div>

				<blockquote>
					<p>Please make sure you have confirm the insurance card is valid</p>
				</blockquote>

			</div>

			<hr>

			<div class="well">
				
				<div class="form-inline">
					<div class="form-group">
						<label>Emergence Person</label>
						<div>
							<input type="text" class="form-control" placeholder="Enter Name" name="contact_name" value="{{ $patient->contact_name ?? '' }}">
						</div>
					</div>
					<div class="form-group">
						<label>Emergence Contact Phone</label>
						<div>
							<input type="text" class="form-control" name="contact_phone" value="{{ $patient->contact_phone ?? '' }}" required>
						</div>
					</div>
				</div>

				<div class="form-inline" >
					<div class="form-group">
						<label>Nationality</label>
						<div>
							{{Form::select('country',
								Widget::getCountries(), isset($patient->country) ? $patient->country:'', array('class'=>'form-control','required'))}}
						</div>
					</div>
					<div class="form-group" id="race">
						<label>Race</label>
						<div>
							{{Form::select('race',
								array(
									'' => 'Select',
									'Black / African American' => 'Black / African American',
									'Asian' => 'Asian',
									'Indian' => 'Indian',
									'Latino' => 'Latino',
									'White' => 'White',
								), isset($patient->race) ? $patient->race:'', array('class'=>'form-control','required'))}}
						</div>
					</div>
				</div>

				<div class="form-group form-width">
					<label>Patient Notes</label>
					<div>
						<textarea class="text_editor" name="notes">{{ $patient->notes ?? '' }}</textarea>
					</div>
				</div>

				<hr>

				<input type="submit" class="btn btn-success" name="button" value="Save Changes">

			</div>


		</div>
	</div>
	</form>
@show