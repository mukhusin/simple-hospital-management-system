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
			
		@if ( Auth::user()->office_id == 6 && $patient->is_paused() )
			
			<div class="btn-group btn-group-justified">
				<a href="#" class=" resume_attendance btn btn-info col-lg-3 big-btn">
					<i class="icon-signin"></i>
					Resume Attendance
				</a>

		@elseif ( Auth::user()->office_id == 6 && $patient->is_new() )
			
			<div class="btn-group btn-group-justified">

				<a href="{{url('patient/edit/'.$patient->id)}}" title="Edit Patient" class="btn btn-primary">
					<i class="icon-edit"></i>
					Edit Patient
				</a>

				<a href="#" class="start_attendance btn btn-success col-lg-3 big-btn">
					<i class="icon-signin"></i>
					New Attendance
				</a>

				<a href="#" class="re_attendance btn btn-danger col-lg-3 big-btn">
					<i class="icon-signin"></i>
					 Re-Attendance
				</a>

		@elseif ( (Auth::user()->isDoctorOffice() && $patient->is_inoffice()) || $patient->is_active_inpatient() )
			
		
			<div class="btn-group btn-group-justified">

				<a href="{{url('patient/edit/vital/'.$attend->patient->id)}}" class="btn btn-info col-lg-3 big-btn">
					<i class="icon-stethoscope"></i>
					Vital Sign
				</a>

				<a href="{{url('patient/edit/allegies/'.$attend->patient->id)}}" class="btn btn-info col-lg-3 big-btn">
					<i class="icon-stethoscope"></i>
					Allegies
				</a>

				<a href="{{url('attendance/clinical/'.$attend->id)}}" class="btn btn-success col-lg-3 big-btn">
					<i class="icon-stethoscope"></i>
					Clinical Information
				</a>

				<a href="{{url('attendance/investigation/'.$attend->id)}}" class="btn btn-primary col-lg-3 big-btn">
					<i class="icon-code"></i>
					 Investigation
				</a>

				<a href="{{url('attendance/treatment/'.$attend->id)}}" class="btn btn-info col-lg-3 big-btn">
					<i class="icon-medkit"></i>
					 Treatment / Checkout
				</a>

				<!-- <a href="#" class="move_patient btn btn-info col-lg-3 big-btn">
					<i class="icon-signin"></i>
					 Send Patient to Other
				</a> -->

		@elseif ( Auth::user()->office_id == 4 && $patient->is_inoffice() )
			
			<div class="btn-group btn-group-justified">

				<a href="{{url('dashboard')}}" class="btn btn-info col-lg-3 big-btn">
					<i class="icon-stethoscope"></i>
					Investigation Work
				</a>


		@elseif ( Auth::user()->office_id == 5 && $patient->is_inoffice() )

			<div class="btn-group btn-group-justified">

				<a href="{{url('attendance/dispense/'.$attend->id)}}" class="pause_attendance btn btn-info col-lg-3 big-btn">
					<i class="icon-signin"></i>
					Drug Dispense
				</a>
				<a href="{{url('patient/return/action/'.$attend->id)}}" class="verify btn btn-danger col-lg-3 big-btn">
					<i class="icon-medkit"></i>
					 Return to Doctor
				</a>


		@elseif ( Auth::user()->office_id == 6 && $patient->is_inoffice() )

			<div class="btn-group btn-group-justified">

				<a href="{{url('attendance/payment/'.$attend->id)}}" class="btn btn-info col-lg-3 big-btn">
					<i class="icon-money"></i>
					Add Payment & Checkout
				</a>
				<a href="{{url('patient/return/action/'.$attend->id)}}" class="verify btn btn-danger col-lg-3 big-btn">
					<i class="icon-medkit"></i>
					 Return to Doctor
				</a>
		@else 

			<div class="btn-group btn-group-justified">
				<div class="bg-danger" style="border-radius:5px" >
					<p style="padding:20px">You have no official access to this patient yet! (She/He is not supposed to be in your office)</p>
				</div>

		@endif

		</div>

			
			<hr>

			<ul class="nav nav-tabs" role="tablist">
				<li class="active">
					<a href="#home" role="tab" data-toggle="tab">Basic Information</a>
				</li>
				<li>
					<a href="#vital" role="tab" data-toggle="tab">Vital Sign</a>
				</li>
				<li>
					<a href="#allegies" role="tab" data-toggle="tab">Allegies</a>
				</li>

				@if (Auth::user()->role_id == 3)
					<li>
						<a href="#history" role="tab" data-toggle="tab">Patient History</a>
					</li>
					<li>
						<a href="#invest" role="tab" data-toggle="tab">Investigation Results</a>
					</li>
				@endif

				<li><a href="#billing" role="tab" data-toggle="tab">Billing Information</a></li>
				
				@if($patient->is_active_inpatient())
					<li><a href="#ward" role="tab" data-toggle="tab">Ward Information</a></li>
				@endif

			</ul>

			<div class="tab-content">

				<div class="tab-pane active" id="home">
					
					<div class="well well1 overflow">
						
						<div class="form-group1">
							<label>Patient ID</label>
							<div>
								P{{str_pad($patient->id,4,'0',STR_PAD_LEFT)}}
							</div>
						</div>

						<div class="form-group1">
							<label>Full Name</label>
							<div>
								{{$patient->title.' '.$patient->name}}
							</div>
						</div>

						<div class="form-group1">
							<label>Registered Date</label>
							<div>
								{{Date('D, d M Y',strtotime($patient->created_at))}}
							</div>
						</div>
						
						<div class="form-group1">
							<label>Age</label>
							<div>
								{{round(floor(time() - strtotime($patient->dob)) / 31556926,3)}}
							</div>
						</div>

						<div class="form-group1">
							<label>Date of Birth</label>
							<div>
								{{Date('D, d M Y',strtotime($patient->dob))}}
							</div>
						</div>
										
						<div class="form-group1">
							<label>Gender</label>
							<div>
								{{$patient->gender}}
							</div>
						</div>

						<div class="form-group1">
							<label>Primary Phone</label>
							<div>
								{{$patient->phone1}}
							</div>
						</div>

						<div class="form-group1">
							<label>Secondary Phone</label>
							<div>
								{{$patient->phone2}}
							</div>
						</div>
					</div>

					<div class="well well2 overflow">
						<div class="form-group1">
							<label>Address</label>
							<div>
								{{$patient->address}}
							</div>
						</div>

						<div class="form-group1">
							<label>Country</label>
							<div>
								{{$patient->country}}
							</div>
						</div>

						<div class="form-group1">
							<label>Race/Ethnicity</label>
							<div>
								{{$patient->race}}
							</div>
						</div>
						
					</div>

					@if ($patient->insurance)
						<div class="well well3 overflow">
							
							<div class="form-group1">
								<label>Insurance Service</label>
								<div>
									{{$patient->insurance->name}}
								</div>
							</div>

							<div class="form-group1">
								<label>Insurance Service ID</label>
								<div>
									{{$patient->sponsor_code}}
								</div>
							</div>
							
						</div>
						
					@endif

					<div class="well well1 overflow">

						<div class="form-group1 form-width">
							<label>Patient Notes</label>
							<div>
								{{$patient->notes}}
							</div>
						</div>

						<hr class="block1">
						
						<div class="form-group1">
							<label>Emergence Person</label>
							<div>
								{{$patient->contact_name}}
							</div>
						</div>

						<div class="form-group1">
							<label>Emergence Contact Phone</label>
							<div>
								{{$patient->contact_phone}}
							</div>
						</div>

						<div class="form-group1">
							<label>File Creator</label>
							<div>
								{{$patient->creator->name}}
							</div>
						</div>

					</div>
															
				</div>

				<div class="tab-pane" id="vital">

					<div class="well well3">
						
						<div class="form-inline" >
							<div class="form-group">
								<label>Temperature</label>
								<div>
									{{ $patient->vitalSign->temperature ?? '-' }}
								</div>
							</div>
							<div class="form-group">
								<label>Height</label>
								<div>
									{{ $patient->vitalSign->height ?? '-' }}
								</div>
							</div>
						</div>

						<div class="form-inline" >
							<div class="form-group">
								<label>Weight</label>
								<div>
									{{ $patient->vitalSign->weight ?? '-' }}
								</div>
							</div>
							<div class="form-group">
								<label>Blood Pressure (BP) </label>
								<div>
									{{ $patient->vitalSign->blood ?? '-' }}
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="allegies">
					<div class="well well2">
						@foreach (explode(',', $patient->allegies) as $m)
							@if ($m)
								<div class="form-inline" >
									<div class="form-group">
										{{$m}} <hr>
									</div>
								</div>
							@endif
						@endforeach
					</div>
				</div>

				@if (Auth::user()->role_id == 3)
					<!-- <div> -->
					<div class="tab-pane well" id="history">
				  		@foreach ($patient->attendances_summary() as $a)
							<div class="well well1 overflow">
				  			<?php
				  			$closed_time = '';

				  			if ($a->closed) {
				  				$closed_time = date('H:i D, d M Y', strtotime($a->created_at));
				  			}
				  			?>
			  				@if ($a->doctor_id)

			  					<div class="form-group2">
									<label>CheckIn ID: </label>
									<div>
										C{{$a->id}}
									</div>
								</div>

								<div class="form-group2">
									<label>Consultant Serve </label>
									<div>
										{{User::find($a->doctor_id)->name}}
									</div>
								</div>

								<div class="form-group2">
									<label>Check In</label>
									<div>
										{{date('H:i D, d M Y',
						  					strtotime($a->created_at))}}
									</div>
								</div>

								<div class="form-group2">
									<label>Check Out</label>
									<div>
										{{$closed_time}}
									</div>
								</div>
								<?php $me1 = []; ?>
		  						@foreach ($a->clinicals as $tem_key => $c)

		  							<div class="block_in">
		  								<h5>
		  									Record updated {{date('H:i D, d M',strtotime($c->created_at))}} by {{ucwords($c->creator->name)}}

											<a style="display: block;" class="text-right vhidden" href="{{ url('attendance/clinical/edit2',$c->id) }}"> more </a>
											<hr class="">
										</h5>

			  							<h5 class="block3">
			  								CLINICAL DETAILS
			  							</h5>

			  							<div class="form-group2">
											<label>Complain </label>
											<div>
												{{$c->complain}}
											</div>
										</div>

										<div class="form-group2">
											<label>Review of other system </label>
											<div>
												{{$c->review}}
											</div>
										</div>

										<div class="form-group2">
											<label>General Examination </label>
											<div>
												{{$c->general_observation}}
											</div>
										</div>

										<div class="form-group2">
											<label>Systemic Examination </label>
											<div>
												{{$c->systemic_observation}}
											</div>
										</div>
	  								
										<h5 class="block1">
											<hr class="block1">
											DIAGNOSIS SUMMARY
										</h5>

										<div class="form-group1">
											<label> Provision </label>
											<div>
												<?php
												$temp = array();
												$e = Widget::get_diagnosis_array($c->provision);
												$t = '';
												?>
												@foreach ($e as $i)
													{{$t.$i}}<?php $t=', '; ?> 
												@endforeach
											</div>
										</div>

										<div class="form-group1">
											<label> Differential </label>
											<div>
												<?php
												$temp = array();
												$e = Widget::get_diagnosis_array($c->differential);
												$t = '';
												?>
												@foreach ($e as $i)
													{{$t.$i}}<?php $t=', '; ?> 
												@endforeach
											</div>
										</div>

										<div class="form-group1">
											<label> Final </label>
											<div>
												<?php
												$temp = array();
												$e = Widget::get_diagnosis_array($c->final);
												$t = '';
												?>
												@foreach ($e as $i)
													{{$t.$i}}<?php $t=', '; ?> 
												@endforeach
											</div>
										</div>
															

		  								<?php
			  								$lr = $c->labResults;
			  							?>
		  								@if (count($lr) > 0)

		  									<h5 class="block1">
		  										<hr class="block1">
		  										LABOLATORY / RADIOLOGY INVESTIGATION SUMMARY
		  									</h5>
		  									<table class="table well well3">
			  									<tr>
					  								<th>Investigation</th>
					  								<th>Results</th>
					  								<th>Attachment </th>
					  								<th>Technician</th>
				  								</tr>

									  		@foreach ($lr as $t)
									  			<tr>
									  				<td>{{LabTest::find($t->test_id)->code}} : {{LabTest::find($t->test_id)->name}}</td>
									  				<td>{{$t->results_register}}</td>
									  				<td>
									  					@if ($t->attachment)
									  						<a href="{{url($t->attachment)}}" title="Download">Download</a>
									  					@endif
									  				</td>
									  				<td>{{User::find($t->creator_id)->name}}</td>
									  			</tr>
									  		@endforeach
		  										
		  									</table>
		  								@endif


								  		<?php
			  								$me = $c->medicals;
			  								$tem1 = count($a->clinicals);
		  									$tem_all = false;
			  								if ($tem1 == $tem_key+1) {
			  									$tem_all = true;
			  								}
			  							?>
								  		@if (count($me) > 0)
								  			<h5 class="block1">
		  										<hr class="block1">
		  										TREATMENT SUMMARY
		  									</h5>
		  									<table class="table well well2">
		  									<tr>
				  								<th>Treatment</th>
				  								<th>Dosage</th>
				  								<th>Dispensed Time</th>
				  								<th>Dispensor</th>
			  								</tr>
									  		@foreach ($me as $t)
									  			<tr>
									  				<td>{{Medical::find($t->medical_id)->name}} - {{Medical::find($t->medical_id)->brand}}</td>
									  				<td>{{$t->dosage}}</td>
									  				<td>{{Date('H:i D, M',strtotime($t->updated_at))}}</td>
									  				<td>
									  					@if ($t->checker_id)
									  						{{User::find($t->checker_id)->name}}
									  					@else
									  						-
									  					@endif
									  				</td>
									  			</tr>
									  		@endforeach
									  		</table>
		  								@endif


		  								@if ($tem_all)

		  									</div>
		  									<div class="block_in">
	
								  			<h5 class="block1">
		  										PATIENT BILL SUMMARY
		  									</h5>
		  									<table class="table well well2">
			  									<tr>
					  								<th>Description</th>
					  								<th>Amount</th>
				  								</tr>
										  		@foreach ($a->bill as $t)
										  			<tr>
										  				<td>{{$t->name}} {{$t->dosage}}</td>
										  				<td>{{number_format($t->amount)}}</td>
										  			</tr>
										  		@endforeach
									  		</table>
		  								@endif
										
		  								</div>
		  						@endforeach

		  						<hr class="block1">
	  							<div class="form-group0">
									<label>Other Service</label>
									<div>
										{{$a->listService()}}
									</div>
								</div>
								<div class="form-group0">
									<label>Consultant Remark </label>
									<div>
										{{$a->remark}}
									</div>
								</div>
			  				@endif
			  				</div>
				  		@endforeach
					</div>

					<div class="tab-pane" id="invest">
						<div class="well well1">
					  		<h3>Patient Investigation Results <hr> </h3>
					  		@if (isset($attend))
						  		<?php $in = $attend->labResults; $temp = 0; ?>
						  		@foreach ($in as $t)
						  			<?php ++$temp; ?>
						  			<div class="form-group">
										<label>{{LabTest::find($t->test_id)->code}} : {{LabTest::find($t->test_id)->name}}</label>
										<div>
											Sample/Specemen: {{$t->sample_register}}
										</div>
										<div>
											Results: {{$t->results_register}}
										</div>
										<div>
											Attachment: @if ($t->attachment)
												<a href="{{url($t->attachment)}}" title="Attachment">Attachment</a>
												@else
												-
												@endif
										</div>
										<div>
											Lab technician: {{User::find($t->creator_id)->name}}
										</div>
									</div>
						  		@endforeach

						  		@if ($temp == 0)
						  			No Results yet
						  		@endif
					  		@endif
						</div>
					</div>
				@endif

				<div class="tab-pane" id="billing">
			  		@if (isset($attend))
			  			<?php

			  			$ab = new AttendanceBill();
			  			$ab = $ab->where('attendance_id','=',$attend->id)->get();

			  			?>

			  			@if (!is_null($attend->insurance))
			  				<h4>Payment covered by: {{$attend->insurance->name}} (Insurance Company)</h4>
			  			@else
			  				<h4>Payment covered by: Patient (Cash)</h4>
			  			@endif

			  			<div class="btn-primary btn-lg">
				  			<table class="table">
				  				<thead>
				  					<tr>
				  						<th>Service</th>
				  						<th>Status</th>
				  						<th>Updated</th>
				  						<th>Amount</th>
				  					</tr>
				  				</thead>
				  				<tbody>
				  					@foreach ($ab as $b)
					  					<tr>
					  						<td>{{$b->name}}</td>
					  						<td>{{$b->status}}</td>
					  						<td>{{date('H:i D, d M Y',strtotime($b->created_at))}}</td>
					  						<td>{{number_format($b->amount)}}</td>
					  					</tr>
				  					@endforeach
				  				</tbody>
				  			</table>
			  				
			  			</div>
			  		@endif
				</div>

				@if($patient->is_active_inpatient())
					<div class="tab-pane" id="ward">
				  		@if (isset($attend))
				  			<?php
				  			$ab = $attend->wardCheck;
				  			?>
				  			<table class="table">
				  				<thead>
				  					<tr>
				  						<th>Ward</th>
				  						<th>Bed</th>
				  						<th>CheckIn</th>
				  						<th>CheckOut</th>
				  						<th>Price</th>
				  						<th>Charges</th>
				  						<th>Assing</th>
				  						<th>Last Update</th>
				  					</tr>
				  				</thead>
				  				<tbody>
				  					@foreach ($ab as $b)
					  					@if($b->checkout)
					  						<tr>
						  						<td>{{$b->ward->name}}</td>
						  						<td>{{$b->bed}}</td>
						  						<td>{{date('H:i D, d M Y',strtotime($b->checkin))}}</td>
						  						<td>{{date('H:i D, d M Y',strtotime($b->checkout))}}</td>
						  						<td>{{number_format($b->ward->price)}}</td>
						  						<td>{{number_format($b->ward->price*(strtotime($b->checkout)-strtotime($b->checkin))/(60*60),2)}}</td>
						  						<td>{{$b->creator->name}}</td>
						  						<td>{{$b->updator->name}}</td>
						  					</tr>
					  					@else 
						  					<tr>
						  						<td>{{$b->ward->name}}</td>
						  						<td>{{$b->bed}}</td>
						  						<td>{{date('H:i D, d M Y',strtotime($b->checkin))}}</td>
						  						<td> - </td>
						  						<td>{{number_format($b->ward->price)}}</td>
						  						<td> - </td>
						  						<td>{{$b->creator->name}}</td>
						  						<td>{{$b->updator->name}}</td>
						  					</tr>
					  					@endif

				  					@endforeach
				  				</tbody>
				  			</table>

				  			@if (Auth::user()->role_id == 3)
					  			<div class="col-lg-12 well">
					  				<form method="POST" action="{{ url()->current() }}">
@csrf
					  				<div class="col-lg-12">
					  					<h4>Change Ward / Bed (This will have impact on Bill) <hr/></h4>
					  				</div>
									<div class="col-lg-3">
										<label>Patient Type </label>
										<?php $temp1 = array('Out','In'); ?>
										<input type="text" name="bed" class="form-control" readonly="readonly" value="{{$temp1[$attend->ward_served]}}" >
									</div>
									<div class="col-lg-3">
										<label>Ward Number </label>
										{{Form::select('ward',Widget::wards(),$attend->ward_id,array('class' => 'form-control ward_served'))}}
									</div>
									<div class="col-lg-3">
										<label>Patient Bed Number / Tag</label>
										<input type="text" name="bed" class="form-control ward_served" value="{{$attend->bed}}" >
									</div>
									<div class="col-lg-3">
										<label>&nbsp;</label>
										<input type="hidden" name="section" value="ward" >
										<input type="submit" name="button" class="form-control btn btn-danger" value="Save Changes" >
									</div>
									</form>
								</div>
							@endif

				  		@endif
					</div>
				@endif

			</div>

		</div>

	</div>

	<div class="modal" id="move_patient">
		<form method="POST" action="{{ url('patient/attendance/start/') }}">
@csrf
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;</button>
						<h4 class="modal-title"> Send Patient </h4>
					</div>
					<div class="modal-body">

						<label>Office to Go</label>
						{{Form::select('office',
							Widget::offices(), '', array(
							'class' => 'form-control','required'
						))}}
						<!-- <hr>
						<p>
							<label> Attachment Notes </label>
							<textarea name="notes" class="text_editor"></textarea>
						</p> -->
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-success" name="button" value="Send Patient">
						<a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal" id="start_attendance">
		<form method="POST" action="{{ url('patient/attendance/start/') }}">
@csrf
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;</button>
						<h4 class="modal-title"> Start Patient Attendance Session </h4>
					</div>
					<div class="modal-body">

						@if ($patient->insurance)
							<div>
								<label>Payment/Billing Method :-</label>
								{{Form::select('payment',
									Widget::paymentMethod($patient->insurance), '', array(
									'class' => 'form-control','required'
								))}}
							</div>
							<hr>
						@endif

						<div>
							<label>Send Patient to :-</label>
							{{Form::select('office',
								Widget::offices(1), '', array(
								'class' => 'form-control','required'
							))}}
						</div>
						<!-- <hr>
						<p>
							<label> Attachment Notes </label>
							<textarea name="notes" class="text_editor"></textarea>
						</p> -->
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-success" name="button" value="Send Patient">
						<a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal" id="re_attendance">
		<form method="POST" action="{{ url('patient/attendance/re/') }}">
@csrf
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;</button>
						<h4 class="modal-title"> Re-attend Patient Session </h4>
					</div>
					<div class="modal-body">

						@if ($patient->insurance)
							<div>
								<label>Payment/Billing Method :-</label>
								{{Form::select('payment',
									Widget::paymentMethod($patient->insurance), '', array(
									'class' => 'form-control','required'
								))}}
							</div>
							<hr>
						@endif

						<div>
							<label>Send Patient to :-</label>
							{{Form::select('office',
								Widget::offices(1), '', array(
								'class' => 'form-control','required'
							))}}
						</div>

						<!-- <hr>
						<p>
							<label> Attachment Notes </label>
							<textarea name="notes" class="text_editor"></textarea>
						</p> -->
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-success" name="button" value="Send Patient">
						<a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="modal" id="resume_attendance">
		<form method="POST" action="{{ url('patient/attendance/start/') }}">
@csrf
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;</button>
						<h4 class="modal-title"> Send Patient </h4>
					</div>
					<div class="modal-body">

						<label>Office to Go</label>
						{{Form::select('office',
							Widget::offices(), '', array(
							'class' => 'form-control','required'
						))}}
						<!-- <hr>
						<p>
							<label> Attachment Notes </label>
							<textarea name="notes" class="text_editor"></textarea>
						</p> -->
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-success" name="button" value="Send Patient">
						<a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</form>

	</div>


	<script type="text/javascript">
	$(document).ready(function() {
		
	});
	</script>

	<!-- End Model -->

	<script type="text/javascript">
	 $(document).ready(function() {

	 	$('.move_patient').click(function(event) {
	 		$('#move_patient').modal('show');
	 	});

	 	$('.start_attendance').click(function(event) {
	 		$('#start_attendance').modal('show');
	 	});

	 	$('.resume_attendance').click(function(event) {
	 		$('#resume_attendance').modal('show');
	 	});

	 	$('.re_attendance').click(function(event) {
	 		$('#re_attendance').modal('show');
	 	});


	});
	</script>
@endsection