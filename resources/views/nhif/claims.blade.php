@extends('master')

@section('main')
<div class="row">
    <div class="col-lg-12">

        @if (session('error'))
            <div class="error-div">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="success-div">{{ session('success') }}</div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading">
                <b>NHIF Claims Dashboard</b>
                <span class="pull-right text-muted">{{ now()->format('F Y') }}</span>
            </div>
            <div class="panel-body">

                {{-- Month / Year Filter --}}
                <form class="form-inline" method="GET" action="{{ url()->current() }}" style="margin-bottom:15px;">
                    <div class="form-group">
                        <label>Year</label>
                        <select name="year" class="form-control" style="width:100px; margin-left:6px;">
                            @for($y = date('Y'); $y >= date('Y')-3; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group" style="margin-left:10px;">
                        <label>Month</label>
                        <select name="month" class="form-control" style="width:120px; margin-left:6px;">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left:10px;">Filter</button>
                </form>

                {{-- Summary Cards --}}
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-md-3">
                        <div class="panel panel-info text-center" style="padding:15px;">
                            <h2 style="margin:0">{{ $folios->count() }}</h2>
                            <small>Total Folios</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-success text-center" style="padding:15px;">
                            <h2 style="margin:0">{{ $folios->where('status','signed')->count() }}</h2>
                            <small>Signed</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning text-center" style="padding:15px;">
                            <h2 style="margin:0">{{ $folios->where('status','submitted')->count() }}</h2>
                            <small>Submitted (Pending Sign)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default text-center" style="padding:15px;">
                            <h2 style="margin:0">TZS {{ number_format($folios->sum('amount_claimed')) }}</h2>
                            <small>Total Claimed</small>
                        </div>
                    </div>
                </div>

                {{-- Monthly Batch Submission --}}
                @if($folios->where('status','signed')->count() > 0)
                <div class="well" style="margin-bottom:20px;">
                    <h4><i class="icon-cloud-upload"></i> Submit Monthly Batch to NHIF</h4>
                    <p class="text-muted">
                        Submit all <strong>{{ $folios->where('status','signed')->count() }}</strong> signed folios
                        for {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }} to NHIF.
                    </p>
                    <button type="button" id="btn-monthly-submit" class="btn btn-danger"
                        data-year="{{ $year }}" data-month="{{ $month }}">
                        <i class="icon-send"></i> Submit Monthly Claim
                    </button>
                    <div id="monthly-submit-result" style="margin-top:10px;"></div>
                </div>
                @endif

                {{-- Folios Table --}}
                <table class="table table-striped table-bordered" id="folios-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Card No</th>
                            <th>Auth No</th>
                            <th>Folio No</th>
                            <th>Amount (TZS)</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($folios as $folio)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($folio->attendance && $folio->attendance->patient)
                                    <a href="{{ url('patient/view/'.$folio->attendance->patient->id) }}">
                                        {{ $folio->attendance->patient->name }}
                                    </a>
                                @else &mdash; @endif
                            </td>
                            <td>{{ $folio->patient_card_no }}</td>
                            <td>{{ $folio->authorization_no ?: '&mdash;' }}</td>
                            <td>{{ $folio->folio_no ?: '&mdash;' }}</td>
                            <td>{{ number_format($folio->amount_claimed) }}</td>
                            <td>
                                <span class="label label-{{
                                    $folio->status === 'signed'     ? 'success' :
                                    ($folio->status === 'submitted' ? 'info'    :
                                    ($folio->status === 'rejected'  ? 'danger'  : 'warning'))
                                }}">
                                    {{ strtoupper($folio->status) }}
                                </span>
                            </td>
                            <td>{{ $folio->submitted_at ? $folio->submitted_at->format('d M Y H:i') : '&mdash;' }}</td>
                            <td>
                                @if($folio->status === 'submitted')
                                <button type="button" class="btn btn-xs btn-success btn-sign-folio"
                                    data-folio="{{ $folio->folio_no }}"
                                    data-attend="{{ $folio->attendance_id }}">
                                    Sign
                                </button>
                                @elseif($folio->status === 'pending')
                                <a href="{{ url('attendance/payment/'.$folio->attendance_id) }}" class="btn btn-xs btn-primary">
                                    Submit
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No NHIF folios found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    var csrf = $('meta[name="csrf-token"]').attr('content');

    // Sign individual folio
    $(document).on('click', '.btn-sign-folio', function() {
        var folioNo  = $(this).data('folio');
        var attendId = $(this).data('attend');
        if (!confirm('Sign folio #' + folioNo + '?')) return;
        var btn = $(this).attr('disabled', true).text('...');
        $.ajax({
            url: '{{ route("nhif.sign-folio") }}',
            method: 'POST',
            data: { _token: csrf, attendance_id: attendId, folio_no: folioNo },
            success: function(res) {
                if (res.success) {
                    alert('Folio signed successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (res.message || 'Signing failed.'));
                    btn.removeAttr('disabled').text('Sign');
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Request failed.'));
                btn.removeAttr('disabled').text('Sign');
            }
        });
    });

    // Monthly batch submission
    $('#btn-monthly-submit').on('click', function() {
        var year  = $(this).data('year');
        var month = $(this).data('month');
        if (!confirm('Submit all signed folios for ' + month + '/' + year + ' to NHIF? This cannot be undone.')) return;
        var btn = $(this).attr('disabled', true).html('<i class="icon-spinner"></i> Submitting...');
        $.ajax({
            url: '{{ route("nhif.attendance.submit-monthly-claim") }}',
            method: 'POST',
            data: { _token: csrf, claim_year: year, claim_month: month },
            success: function(res) {
                if (res.success) {
                    $('#monthly-submit-result').html('<div class="alert alert-success">Monthly claim submitted successfully!</div>');
                    setTimeout(function(){ location.reload(); }, 2000);
                } else {
                    $('#monthly-submit-result').html('<div class="alert alert-danger">' + (res.message || 'Submission failed.') + '</div>');
                    btn.removeAttr('disabled').html('<i class="icon-send"></i> Submit Monthly Claim');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Request failed.';
                $('#monthly-submit-result').html('<div class="alert alert-danger">' + msg + '</div>');
                btn.removeAttr('disabled').html('<i class="icon-send"></i> Submit Monthly Claim');
            }
        });
    });
});
</script>
@endsection
