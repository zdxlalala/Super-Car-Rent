<?php

$page = 'Home';
include 'Header.php';
include 'Helper.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SuperCarRent</title>
    <style>
        img {
            width: 100%;
            height: auto;
        }
        .aside {
            background-color: #4CAF50;
            padding: 15px;
            color: #ffffff;
            text-align: center;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        }
        .button {
            background-color: #111; 
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            -webkit-transition-duration: 0.4s; 
            transition-duration: 0.4s;
        }
        .button:hover {
            box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
        }
        .button span {
            cursor: pointer;
            display: inline-block;
            position: relative;
            transition: 0.5s;
        }

        .button span:after {
            content: '\00bb';
            position: absolute;
            opacity: 0;
            top: 0;
            right: -20px;
            transition: 0.5s;
        }

        .button:hover span {
            padding-right: 25px;
        }       

        .button:hover span:after {
        opacity: 1;
        right: 0;
        }
    </style>
    
</head>
<body>
    
    <img src="home.jpg" >
    <div class="col-3 col-s-12">
    <div class="aside">
      <h1>Need a ride?</h1>
      <p>Super Car Rental is Vancouver's BEST car rental company.</p>
      <h1>Where?</h1>
      <p>The two major branches in UBC and SFU cover both Vancouver and Burnaby to provide COVINENCE.</p>
      <h1>How?</h1>
      <p>Start by making a reservation or rent directly!</p>
      <a href="SCRRental.php" class="button"><span>Start Your Ride Now! </span></a>
    </div>
    </div>

</body>
</html>
