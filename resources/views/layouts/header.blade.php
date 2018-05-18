<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Specific -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>{{ isset($data['title']) ? $data['title'] : "" }} | Geniota.com |</title>

    <!-- Fav Icon -->
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-16x16.png" sizes="16x16" />

    <!-- Bootstrap CSS -->
    <link href="{{ secure_url('/public') }}/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- Magnific popup CSS -->
    <link href="{{ secure_url('/public') }}/css/animate.css" rel="stylesheet">

    <!-- WaitMe CSS -->
    <link href="{{ secure_url('/public') }}/css/waitMe.min.css" rel="stylesheet">

    <!-- Custom srcoll bar -->
    <link rel="stylesheet" type="text/css" href="{{ secure_url('/public') }}/plugins/custom-scroll-bar/jquery.mCustomScrollbar.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,600,600i,800,800i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">

    <!-- Material icon CSS -->
    <link href="{{ secure_url('/public') }}/css/material-design-iconic-font.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

    <!-- Main CSS -->
    <link href="{{ secure_url('/public') }}/css/style.css" rel="stylesheet">
    <link href="{{ secure_url('/public') }}/css/tranding.css" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="{{ secure_url('/public') }}/css/responsive.css" rel="stylesheet">



</head>
