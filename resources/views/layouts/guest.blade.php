<!DOCTYPE html>
<html lang="en">

<head>
	<title>dentistatech
		<img src="{{ asset('favicon.ico') }}" alt="Logo">
	</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{ asset('logintemplate/images/icons/favicon.ico') }}" />
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/bootstrap/css/bootstrap.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css"
		href="{{ asset('logintemplate/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css"
		href="{{ asset('logintemplate/fonts/iconic/css/material-design-iconic-font.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/animate/animate.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/css-hamburgers/hamburgers.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/animsition/css/animsition.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/select2/select2.min.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css"
		href="{{ asset('logintemplate/vendor/daterangepicker/daterangepicker.css') }}">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/css/main.css') }}">
	<!--===============================================================================================-->
	<style>
	/* Fonte */
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

	*{
	margin:0;
	padding:0;
	box-sizing:border-box;
	font-family:'Poppins',sans-serif;
	}

	body{
	min-height:100vh;
	display:flex;
	justify-content:center;
	align-items:center;
	background:
	radial-gradient(circle at top left,#eef6ff,#ffffff 45%),
	linear-gradient(135deg,#f8fbff,#edf5ff);
	overflow:hidden;
	position:relative;
	}

	/* Fundo com ondas */
	body::before{
	content:'';
	position:absolute;
	inset:0;
	background:url("../images/bg-wave.svg") no-repeat bottom center;
	background-size:cover;
	opacity:.25;
	pointer-events:none;
	}

	/* Pontos decorativos */
	body::after{
	content:'';
	position:absolute;
	width:260px;
	height:260px;
	right:80px;
	bottom:120px;
	background-image: radial-gradient(#56b9ff 2px, transparent 2px);
	background-size:18px 18px;
	opacity:.35;
	}

	/* Container */

	.login-page{
	width:100%;
	max-width:1400px;
	display:flex;
	flex-direction:column;
	align-items:center;
	padding:40px;
	}

	/* Logo */

	.logo{
	width:650px;
	margin-bottom:15px;
	}

	.slogan{
	font-size:28px;
	letter-spacing:8px;
	color:#15386d;
	font-weight:500;
	margin-bottom:60px;
	}

	.slogan::before,
	.slogan::after{
	content:"";
	width:120px;
	height:4px;
	background:#24b4ff;
	display:inline-block;
	vertical-align:middle;
	margin:0 25px;
	}

	/* Card */

	.login-card{

	width:620px;
	background:#fff;
	border-radius:28px;
	padding:45px;
	box-shadow:
	0 30px 60px rgba(12,71,140,.12);
	}

	/* Labels */

	.login-card label{
	display:block;
	margin-bottom:10px;
	font-weight:700;
	font-size:24px;
	color:#163a72;
	}

	/* Inputs */

	.input-group{
	position:relative;
	margin-bottom:30px;
	}

	.input-group input{

	width:100%;
	height:72px;

	border:2px solid #d6e5ff;
	border-radius:15px;

	padding-left:65px;
	padding-right:55px;

	font-size:22px;

	outline:none;

	transition:.3s;
	}

	.input-group input:focus{

	border-color:#1e88ff;
	box-shadow:0 0 12px rgba(30,136,255,.25);

	}

	.input-group i{

	position:absolute;
	left:22px;
	top:50%;
	transform:translateY(-50%);
	color:#7b8aa3;
	font-size:22px;

	}

	/* Checkbox */

	.options{

	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-top:10px;

	}

	.options label{

	display:flex;
	align-items:center;
	gap:10px;

	font-size:20px;
	font-weight:500;
	}

	.options input{

	width:22px;
	height:22px;

	}

	/* Link */

	.options a{

	color:#0f74ff;
	text-decoration:none;
	font-size:19px;
	}

	.options a:hover{

	text-decoration:underline;

	}

	/* Botão */

	.btn-login{

	width:280px;
	height:72px;

	border:none;
	border-radius:16px;

	background:linear-gradient(90deg,#003d84,#0a74ff);

	color:#fff;

	font-size:24px;
	font-weight:700;

	cursor:pointer;

	transition:.3s;
	}

	.btn-login:hover{

	transform:translateY(-3px);
	box-shadow:0 20px 35px rgba(0,74,173,.35);

	}

	/* Rodapé */

	.footer{

	margin-top:45px;
	color:#6c7c97;
	font-size:18px;

	}

	@media(max-width:768px){

	.logo{
	width:320px;
	}

	.slogan{
	font-size:16px;
	letter-spacing:2px;
	}

	.slogan::before,
	.slogan::after{
	width:40px;
	}

	.login-card{

	width:100%;
	padding:30px;

	}

	.btn-login{
	width:100%;
	}

	}
	</style>
</head>

<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
					<div>
						<a href="/">
							<x-application-logo class="w-20 h-20 fill-current text-gray-500" />
						</a>
					</div>

					<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
						{{ $slot }}
					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="dropDownSelect1"></div>

	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/animsition/js/animsition.min.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/bootstrap/js/popper.js') }}"></script>
	<script src="{{ asset('logintemplate/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/select2/select2.min.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/daterangepicker/moment.min.js') }}"></script>
	<script src="{{ asset('logintemplate/vendor/daterangepicker/daterangepicker.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/vendor/countdowntime/countdowntime.js') }}"></script>
	<!--===============================================================================================-->
	<script src="{{ asset('logintemplate/js/main.js') }}"></script>

</body>

</html>