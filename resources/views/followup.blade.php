@extends('master')

@section('main')
	<div class="row">

		<script type="text/javascript">
			$(document).ready(function() {

				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					defaultDate: '{{date('Y-m-d')}}',
					editable: false,
					eventSources: [
						{
				            url: '{{url('followup/get')}}',
				            type: 'GET',
				            data: {
				                custom_param1: '1'
				            },
				            error: function() {
				                alert('there was an error while fetching events!');
				            },
				            color: 'green',   // a non-ajax option
				            textColor: 'white' // a non-ajax option
				        }
			        ],
			        eventClick: function(event) {
				        if (event.id) {
				            console.log(event.id);
				        }
				    },
				    timeFormat: 'h:mm A'
				});
			});		
		</script>

		<div class="col-lg-12">
			<div id="calendar"></div>
        </div>

	</div>

@endsection