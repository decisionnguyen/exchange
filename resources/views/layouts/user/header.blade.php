<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Specific -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title -->
    <title>Geniota.com | {{ isset($data['title']) ? $data['title'] : "" }}</title>

    <!-- Fav Icon -->
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ secure_url('/public') }}/img/fav-icon/favicon-16x16.png" sizes="16x16" />

    <!-- Bootstrap CSS -->
    <link href="{{ secure_url('/public') }}/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">

    <!-- Material icon CSS -->
    <link href="{{ secure_url('/public') }}/css/material-design-iconic-font.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ secure_url('/public') }}/css/style.css" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="{{ secure_url('/public') }}/css/responsive.css" rel="stylesheet">

</head>
<body>