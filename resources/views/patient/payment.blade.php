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
				
			<div class="panel panel-default">
			    <div class="panel-heading">
			    	{{$patient->name}} Bill
			    </div>
			    <div class="panel-body">
					<table class="table">
						<thead>
							<tr>
								<th>Bill Description</th>
								<th style="float:right">Bill Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$ab = new AttendanceBill();
				  			$ab = $ab->where('attendance_id','=',$attend->id);
				  			$ab = $ab->where('status','=','unpaid');
				  			$ab = $ab->get();
				  			$sum = 0;
							?>
							@foreach ($ab as $b)
								{{'';$sum+=$b->amount}}
								<tr>
									<td>
										{{$b->name}}
									</td>
									<td align="right">
										{{number_format($b->amount)}}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
			    	
			    </div>
			</div>

			<div class="well">
				
				<h3>Total Amount is {{number_format($sum)}} Tshs</h3>

				<?php
				$l = strlen(number_format($sum));
				?>

				@php
					$insurance   = $attend->insurance_id > 0 ? $attend->insurance : null;
					$isNhif      = $insurance && $insurance->isNhif();
				@endphp

				@if ($isNhif)
					{{-- ===== NHIF INSURANCE PATIENT FLOW ===== --}}
                    <hr>
                    @php
                        $nhifFolio   = $attend->nhifFolio;
                        $cardNo      = $attend->nhif_card_no ?? $patient->nhif_card_no ?? '';
                        $authNo      = $attend->nhif_authorization_no ?? '';
                        $claimStatus = $attend->nhif_claim_status ?? 'pending';
                    @endphp

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <b>NHIF Claim Information</b>
                            <span class="pull-right label label-info">{{ $insurance->name }}</span>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <th width="40%">NHIF Card No</th>
                                    <td>{{ $cardNo ?: '<span class="text-danger">Not captured</span>' }}</td>
                                </tr>
                                <tr>
                                    <th>Authorization No</th>
                                    <td>{{ $authNo ?: '<span class="text-muted">None</span>' }}</td>
                                </tr>
                                <tr>
                                    <th>Folio Status</th>
                                    <td>
                                        @if($nhifFolio)
                                            <span class="label label-{{ $nhifFolio->status === 'signed' ? 'success' : ($nhifFolio->status === 'submitted' ? 'info' : 'warning') }}">
                                                {{ strtoupper($nhifFolio->status) }}
                                            </span>
                                            @if($nhifFolio->folio_no) &mdash; Folio #{{ $nhifFolio->folio_no }} @endif
                                        @else
                                            <span class="label label-default">NOT SUBMITTED</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- Step 1: Request Bill Confirmation (OTP) --}}
                            @if(!$nhifFolio || $nhifFolio->isPending())
                            <div id="nhif-step-1" class="nhif-step">
                                <h4><span class="badge">1</span> Request Bill Confirmation (OTP)</h4>
                                <p class="text-muted small">NHIF will send a confirmation code to the patient's registered phone.</p>
                                <div class="form-inline">
                                    <input type="text" id="nhif-card-input" class="form-control" placeholder="NHIF Card No" value="{{ $cardNo }}" style="width:200px">
                                    <button type="button" id="btn-request-otp" class="btn btn-primary">
                                        <i class="icon-mobile-phone"></i> Request OTP
                                    </button>
                                </div>
                                <div id="otp-result" style="display:none; margin-top:10px;"></div>

                                {{-- Step 2: Confirm code --}}
                                <div id="nhif-step-2" style="display:none; margin-top:15px;">
                                    <h4><span class="badge">2</span> Enter Confirmation Code</h4>
                                    <div class="form-inline">
                                        <input type="text" id="nhif-otp-code" class="form-control" placeholder="6-digit code" maxlength="6" style="width:150px">
                                        <button type="button" id="btn-confirm-otp" class="btn btn-success">
                                            <i class="icon-check"></i> Verify Code
                                        </button>
                                    </div>
                                    <div id="otp-confirm-result" style="margin-top:10px;"></div>
                                </div>

                                {{-- Step 3: Submit Folio --}}
                                <div id="nhif-step-3" style="display:none; margin-top:15px;">
                                    <h4><span class="badge">3</span> Submit Folio to NHIF</h4>
                                    <p class="text-muted small">Submits all bill items to NHIF for this visit.</p>
                                    <button type="button" id="btn-submit-folio" class="btn btn-warning">
                                        <i class="icon-cloud-upload"></i> Submit Folio
                                    </button>
                                    <div id="folio-submit-result" style="margin-top:10px;"></div>
                                </div>
                            </div>
                            @endif

                            {{-- If folio submitted, show sign option --}}
                            @if($nhifFolio && $nhifFolio->isSubmitted())
                            <div id="nhif-sign-step">
                                <h4><span class="badge">4</span> Sign Folio</h4>
                                <p class="text-muted small">Digitally sign the submitted folio to finalise the NHIF claim.</p>
                                <button type="button" id="btn-sign-folio" class="btn btn-success" data-folio="{{ $nhifFolio->folio_no }}">
                                    <i class="icon-pencil"></i> Sign Folio #{{ $nhifFolio->folio_no }}
                                </button>
                                <div id="folio-sign-result" style="margin-top:10px;"></div>
                            </div>
                            @endif

                            {{-- Signed: ready for checkout --}}
                            @if($nhifFolio && $nhifFolio->isSigned())
                            <div class="alert alert-success">
                                <i class="icon-ok-circle"></i> NHIF claim successfully signed. Patient may be checked out.
                            </div>
                            @endif
                        </div>
                    </div>

                    <hr>
                    <input type="submit" name="button" class="btn btn-success" value="Print Invoice and Checkout">

				@elseif ($insurance)
					{{-- ===== OTHER INSURANCE (non-NHIF) ===== --}}
					<hr>
					<div class="alert alert-info">
						<i class="icon-shield"></i>
						Insurance: <strong>{{ $insurance->name }}</strong>
						@if($attend->insurance_number) &mdash; No: {{ $attend->insurance_number }} @endif
					</div>
					<hr>
					<input type="submit" name="button" class="btn btn-success" value="Print Invoice and Checkout">

				@else
					{{-- ===== CASH PATIENT ===== --}}
					<hr>
					<input id="paid" type="text" name="paid" class="autoNumber form-control"
						data-v-max="{{$sum*10}}.00" data-v-min="0"
						pattern=".{<?php echo $l-1 ?>,<?php echo $l+1?>}"
						placeholder="Enter Tendered Amount" required>
					<hr>
					<input type="submit" name="button" class="btn btn-success" value="Submit Payment and Checkout">
				@endif
			</div>



			</form>
			
	</div>

</div>

<script type="text/javascript">
	jQuery(function($) {
	    $('.autoNumber').autoNumeric('init');
	});
	$(document).ready(function() {
		$('form').on("keyup keypress", function(e) {
		    var code = e.keyCode || e.which; 
		    if (code  == 13) {               
		        e.preventDefault();
		        return false;
		    }
		});
		
                // ===== NHIF FLOW =====
                @if ($isNhif)
                var attendId = {{ $attend->id }};
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Step 1: Request OTP
                $('#btn-request-otp').on('click', function() {
                    var cardNo = $('#nhif-card-input').val().trim();
                    if (!cardNo) { alert('Please enter the NHIF card number.'); return; }
                    var btn = $(this).attr('disabled', true).text('Sending...');
                    $.ajax({
                        url: '{{ route("nhif.attendance.request-otp") }}',
                        method: 'POST',
                        data: { _token: csrfToken, card_no: cardNo, attendance_id: attendId },
                        success: function(res) {
                            if (res.success) {
                                $('#otp-result').html('<div class="alert alert-success">OTP sent! Enter the code below.</div>').show();
                                $('#nhif-step-2').show();
                            } else {
                                $('#otp-result').html('<div class="alert alert-danger">' + (res.message || 'Failed to send OTP.') + '</div>').show();
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Request failed.';
                            $('#otp-result').html('<div class="alert alert-danger">' + msg + '</div>').show();
                        },
                        complete: function() { btn.removeAttr('disabled').html('<i class="icon-mobile-phone"></i> Request OTP'); }
                    });
                });

                // Step 2: Confirm OTP
                $('#btn-confirm-otp').on('click', function() {
                    var code = $('#nhif-otp-code').val().trim();
                    var cardNo = $('#nhif-card-input').val().trim();
                    if (!code) { alert('Please enter the confirmation code.'); return; }
                    var btn = $(this).attr('disabled', true).text('Verifying...');
                    $.ajax({
                        url: '{{ route("nhif.get-bill-confirmation") }}',
                        method: 'GET',
                        data: { card_no: cardNo, confirmation_code: code },
                        success: function(res) {
                            if (res.success) {
                                $('#otp-confirm-result').html('<div class="alert alert-success">Code verified! Proceed to submit folio.</div>');
                                $('#nhif-step-3').show();
                            } else {
                                $('#otp-confirm-result').html('<div class="alert alert-danger">' + (res.message || 'Invalid code.') + '</div>');
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Verification failed.';
                            $('#otp-confirm-result').html('<div class="alert alert-danger">' + msg + '</div>');
                        },
                        complete: function() { btn.removeAttr('disabled').html('<i class="icon-check"></i> Verify Code'); }
                    });
                });

                // Step 3: Submit Folio
                $('#btn-submit-folio').on('click', function() {
                    if (!confirm('Submit this visit\'s bills to NHIF as a folio?')) return;
                    var btn = $(this).attr('disabled', true).text('Submitting...');
                    $.ajax({
                        url: '{{ route("nhif.attendance.submit-folio") }}',
                        method: 'POST',
                        data: { _token: csrfToken, attendance_id: attendId },
                        success: function(res) {
                            if (res.success) {
                                $('#folio-submit-result').html('<div class="alert alert-success">Folio submitted! Folio #' + (res.data.FolioNo || '') + '. Reload to sign.</div>');
                                setTimeout(function(){ location.reload(); }, 2000);
                            } else {
                                $('#folio-submit-result').html('<div class="alert alert-danger">' + (res.message || 'Submission failed.') + '</div>');
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Submission failed.';
                            $('#folio-submit-result').html('<div class="alert alert-danger">' + msg + '</div>');
                        },
                        complete: function() { btn.removeAttr('disabled').html('<i class="icon-cloud-upload"></i> Submit Folio'); }
                    });
                });

                // Step 4: Sign Folio
                $('#btn-sign-folio').on('click', function() {
                    var folioNo = $(this).data('folio');
                    if (!confirm('Sign folio #' + folioNo + '? This finalises the NHIF claim.')) return;
                    var btn = $(this).attr('disabled', true).text('Signing...');
                    $.ajax({
                        url: '{{ route("nhif.attendance.sign-folio") }}',
                        method: 'POST',
                        data: { _token: csrfToken, attendance_id: attendId, folio_no: folioNo },
                        success: function(res) {
                            if (res.success) {
                                $('#folio-sign-result').html('<div class="alert alert-success">Folio signed successfully! Reloading...</div>');
                                setTimeout(function(){ location.reload(); }, 2000);
                            } else {
                                $('#folio-sign-result').html('<div class="alert alert-danger">' + (res.message || 'Signing failed.') + '</div>');
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Signing failed.';
                            $('#folio-sign-result').html('<div class="alert alert-danger">' + msg + '</div>');
                        },
                        complete: function() { btn.removeAttr('disabled').html('<i class="icon-pencil"></i> Sign Folio #' + folioNo); }
                    });
                });
                @endif
@endsection