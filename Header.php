<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
    .header {
        background-color: #f1f1f1;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    ul {
        list-style-type: none;
        position: sticky;
        top: 0;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
    }
    li {
        float: left;
        border-right:1px solid #bbb;
    }
    li:last-child {
        border-right: none;
    }
    li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }
    li a:hover:not(.active) {
        background-color: #111;
    }
    .active {
        background-color: #4CAF50;
    }
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Car Rental</h1>
    </div>

    <ul>
        <li>
            <a 
                <?php echo ($page == 'Home') ? "class='active'" : ""; ?> 
                href="SuperCarRent.php">Home
            </a>
        </li>
        <li>
            <a 
                <?php echo ($page == 'Rental') ? "class='active'" : ""; ?> 
                href="SCRRental.php">Rental
            </a>
        </li>
        <li>
            <a 
                <?php echo ($page == 'Return') ? "class='active'" : ""; ?> 
                href="CarReturn.php">Return
            </a>
        </li>
        <li><a href="#About">About</a></li>
        <li style="float:right">
            <a
                <?php echo ($page == 'SignUp') ? "class='active'" : ""; ?> 
                href="SignUp.php" target="_blank">Sign Up
            </a>
        </li>
    </ul>
</body>
</html>