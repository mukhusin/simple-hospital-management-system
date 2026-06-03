@extends('master')

@php
    use App\Models\User;
    use App\Models\Insurance;
    use App\Models\LabTest;
    use App\Models\Medical;
    use App\Models\Widget;
    use App\Models\AttendanceBill;
@endphp

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
						<select name="gender" class="form-control" required>
							<option value="">Select</option>
							<option value="Male" {{ ($patient->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
							<option value="Female" {{ ($patient->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
						</select>
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
						<select name="sponsor" id="sponsor" class="form-control chosen-select-width" data-placeholder="Select Insurance">
							<option value="">Select Insurance</option>
							@foreach(Widget::getInsurance() as $id => $name)
								<option value="{{ $id }}" {{ ($patient->sponsor ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group" id="sponsor_code">
					<label>Membership Number</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Membership No" name="sponsor_code" value="{{ $patient->sponsor_code ?? '' }}">
					</div>
				</div>

				{{-- NHIF Card Number with live verification --}}
				<div class="form-group" id="nhif_card_group">
					<label>NHIF Card No</label>
					<div class="input-group" style="width:280px">
						<input type="text" id="nhif_card_no" class="form-control" placeholder="e.g. 05-12345-6" name="nhif_card_no" value="{{ $patient->nhif_card_no ?? '' }}">
						<span class="input-group-btn">
							<button type="button" id="btn-verify-nhif" class="btn btn-info">Verify</button>
						</span>
					</div>
					<div id="nhif-verify-result" style="margin-top:6px; font-size:12px;"></div>
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
						<select name="country" id="country" class="form-control chosen-select-width" data-placeholder="Select Country" required>
							<option value="">Select Country</option>
							@foreach(Widget::getCountries() as $val => $label)
								<option value="{{ $val }}" {{ ($patient->country ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="form-group" id="race">
						<label>Race</label>
						<div>
						<select name="race" class="form-control" required>
							<option value="">Select</option>
							<option value="Black / African American" {{ ($patient->race ?? '') == 'Black / African American' ? 'selected' : '' }}>Black / African American</option>
							<option value="Asian" {{ ($patient->race ?? '') == 'Asian' ? 'selected' : '' }}>Asian</option>
							<option value="Indian" {{ ($patient->race ?? '') == 'Indian' ? 'selected' : '' }}>Indian</option>
							<option value="Latino" {{ ($patient->race ?? '') == 'Latino' ? 'selected' : '' }}>Latino</option>
							<option value="White" {{ ($patient->race ?? '') == 'White' ? 'selected' : '' }}>White</option>
						</select>
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updateInsuranceFields() {
        var val = $('#sponsor').val();
        var txt = $('#sponsor').find('option:selected').text().toLowerCase();
        var isNhif = txt.indexOf('nhif') !== -1;

        if (!val) {
            $('#sponsor_code').hide();
            $('#nhif_card_group').hide();
        } else if (isNhif) {
            $('#sponsor_code').show();
            $('#nhif_card_group').show();
        } else {
            $('#sponsor_code').show();
            $('#nhif_card_group').hide();
        }
    }

    updateInsuranceFields();
    $('#sponsor').on('change', updateInsuranceFields);

    $('#btn-verify-nhif').on('click', function() {
        var cardNo = $('#nhif_card_no').val().trim();
        if (!cardNo) { alert('Enter NHIF card number first.'); return; }
        var btn = $(this).attr('disabled', true).text('...');
        var result = $('#nhif-verify-result');
        result.html('<span class="text-muted">Verifying...</span>');
        $.get('{{ route("nhif.card-details-frontend") }}', { card_no: cardNo }, function(res) {
            if (res.success && res.data) {
                var d = res.data;
                result.html(
                    '<span class="text-success"><b>Valid</b></span> &mdash; ' +
                    (d.FirstName || '') + ' ' + (d.LastName || '') +
                    (d.SchemeCode ? ' &bull; Scheme: ' + d.SchemeCode : '') +
                    (d.ProductCode ? ' &bull; Product: ' + d.ProductCode : '')
                );
            } else {
                result.html('<span class="text-danger">Card not found or invalid: ' + (res.message || '') + '</span>');
            }
        }).fail(function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Verification failed.';
            result.html('<span class="text-danger">' + msg + '</span>');
        }).always(function() {
            btn.removeAttr('disabled').text('Verify');
        });
    });
});
</script>
@endpush