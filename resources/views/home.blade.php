<!DOCTYPE html>
<html lang="en">

<head>
<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>

    <div id="app">
        <App />
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>

<style>
    ::-webkit-scrollbar {
      width: 10px !important;
    }
    
    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1 !important; 
   }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: rgb(167, 167, 167) !important;
        border-radius: 10px;
    }
    
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: rgb(129, 129, 129) !important; 
    }
    </style>