<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta http-equiv="x-ua-compatible" content="ie=edge">

   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/assets/images/favicon.png')}}">
   <title>Admin</title>

   <!-- Styles -->
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   
   <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

   <!-- Font Awesome 
   <script src="https://kit.fontawesome.com/d94868da38.js" crossorigin="anonymous"></script>-->
</head>

<body class="hold-transition sidebar-mini">
   wqwqv {{csrf_token()}}
   <h2>cds</h2>
   <!--<div id="app">
      <App />
  </div>-->

  <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>