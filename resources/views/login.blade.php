<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="Visitor" >
        <title>HosCare</title>

        <link rel="stylesheet" href="{{ asset('assets/css/default.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script src="{{ asset('assets/js/default.js') }}"></script>

    </head>
    <body style="background:rgb(235, 234, 231)">
		<div class="row">
			<div class="container">
				<div class="col-lg-4 col-centered ">
					<div class="login-wrapper" style="border:none;">
			        	<form method="POST" action="{{ url()->current() }}">
@csrf

			        	<div class="panel panel-default">
						  <div class="panel-heading">
						    <h3 class="panel-title">
						    	<img src="{{url('images/logo.png')}}" width="50" alt="" style="margin-left:42%">
						    </h3>
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
						  	<div class="panel-body">
								<input name="username" class="form-control m5" type="text" placeholder="Username">
					            <input name="password" class="form-control m5" type="password" placeholder="Password">
					            <button type="submit" class="btn btn-lg btn-primary btn-block">
					            	login
					            </button>
						  	</div>
						</div>

			        	
			        	<!-- <hr style="border: 1px solid red;" > -->
			        	<!-- <h2>HosCare </h2> -->

				        

			            

			            <hr style="border: 1px solid red;" >
			            <span id="login_sign">HosCare v1.2 - CapsLock LTD</span>
			            
			            </form>        
			        </div>
				</div>
			</div>
		</div>
    </body>
</html>

