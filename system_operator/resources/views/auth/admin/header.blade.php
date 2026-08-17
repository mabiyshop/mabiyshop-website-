<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.png') }}" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  </head>
  <body>

    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one"  

          @if(\Request::server('HTTP_HOST') == 'seller.biponi.com' || \Request::server('HTTP_HOST') == '127.0.0.1:8001')
            style="background: url(/backend/assets/images/seller-bg.jpg)no-repeat;background-size:cover;"
          @else
            style="background: url(/backend/assets/images/background.jpg)no-repeat;background-size:cover;"
          @endif
        
           
        
        >
          <div class="row w-100">

