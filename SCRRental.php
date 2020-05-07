<html>
    <head>
        <title>Car Rental</title>
        <style>
            input[type=text], select {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            }

            input[type=submit] {
                width: 100%;
                background-color: #4CAF50;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            input[type=submit]:hover {
                background-color: #45a049;
            }   

            div {
                border-radius: 5px;
                background-color: #f2f2f2;
                padding: 20px;
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
        </style>
    </head>

    <body>
    <?php 
        $page = 'Rental';
        include 'Header.php';
        include 'Helper.php';
    ?>
    <div>
    <h2>Find availabe car</h2> <br>
    <form method = "POST" action = "./SCRRental.php">
        <label for="specialCarType"><b>Special Car Type:</b></label>
        <input type="text" name="cartype" placeholder="Car type..." value="<?php echo isset($_POST['cartype']) ? $_POST['cartype'] : '' ?>" />
        <br>
		<label for="location"><b>Location:</b></label>
        <input type="text"  name="locationName" placeholder="Location..." value="<?php echo isset($_POST['locationName']) ? $_POST['locationName'] : '' ?>" />
        <br>
		<label for="timeFrom"><b>Time From:</b></label>
        <input type="text"  name="timeFromWhen" placeholder="From..." value="<?php echo isset($_POST['timeFromWhen']) ? $_POST['timeFromWhen'] : '' ?>" />
        <br>
		<label for="timeTo"><b>Time To:</b></label>
        <input type="text"  name="timeToWhen" placeholder="To..." value="<?php echo isset($_POST['timeToWhen']) ? $_POST['timeToWhen'] : '' ?>" />
        <br>
        <input type="submit" value="Search" name='search'>
        <br>
        <label for="avalibaleCar"><b>Avaiable Car Number:
            <?php 
            global $db_conn;
            if ($db_conn) {
                if (array_key_exists('search', $_POST)) {
                    $result = executePlainSQL("select count(*)
                     from vehicle 
                     where status = 'Available'
                     and (vtname = '".$_POST["cartype"]."' or '".$_POST["cartype"]."' is null)
                     and (location = '".$_POST["locationName"]."'or '".$_POST["locationName"]."' is null)
                     and vtname not in (select vtname from reservation 
                     where ((to_date('".$_POST["timeFromWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime) 
                     or (to_date('".$_POST["timeToWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime)))");
                     while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                             $string = "";
                             $string = $row["COUNT(*)"];
                            echo isset($string) ? $string : '';
                            }
                        }
                        OCILogoff($db_conn);
                    } else {
                        echo "cannot connect";
                        $e = OCI_Error(); // For OCILogon errors pass no handle
                        echo htmlentities($e['message']);
                    }
                ?>
            
            </b></label>
            <br>
            <button class="button" type="submit" name='showMore'>Show More</button>
            <br>
            <?php 
            global $db_conn;
            if ($db_conn) {
            if (array_key_exists('showMore', $_POST)) {
                $result = executePlainSQL("select *
                from vehicle where (status = 'Available') 
                and (vtname = '".$_POST["cartype"]."' or '".$_POST["cartype"]."' is null)
                and (location = '".$_POST["locationName"]."'or '".$_POST["locationName"]."' is null)
                and vtname not in (select vtname from reservation 
                where ((to_date('".$_POST["timeFromWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime) 
                or (to_date('".$_POST["timeToWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime)))
                order by vlicense");
                $columnNames = array("vlicense","make","model","year","color","odometer","status","vtname","location","city");
                printTable($result, $columnNames);
            } 
            OCILogoff($db_conn);
            } else {
                echo "cannot connect";
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
            }
        ?>
        
    </form>
    </div>

    <br>
    <hr />
    <br>

    <div>
    <h2>Make Reservation</h2> <br>
    <form method = "POST" action = "./SignUp.php">
            <button class = "button" type="submit" name='newCustmer'>New Custmer</button>
    </form>
    <br>
    <form method = "POST" action = "./SCRRental.php">
                <label for="vehical num"><b>Vehical Type Name:</b></label>
                <input type="text" name="vtname" placeholder="Type..." value="<?php echo isset($_POST["vtname"]) ? $_POST["vtname"] : ''?>" />
                <br>
                <label for="driverLicence"><b>Driver Licence Numebr:</b></label>
                <input type="text" name="dlicense" placeholder="Driver License #..." value="<?php echo isset($_POST["dlicense"]) ? $_POST["dlicense"] : ''?>" />
                <br>
                <label for="retimeFrom"><b>Reservation Time From:</b></label>
                <input type="text"  name="reTimeFromWhen" placeholder="From..." value="<?php echo isset($_POST["reTimeFromWhen"]) ? $_POST["reTimeFromWhen"] : ''?>" />
                <br>
		        <label for="retimeTo"><b>Reservation Time To:</b></label>
                <input type="text"  name="reTimeToWhen" placeholder="To..." value="<?php echo isset($_POST["reTimeToWhen"]) ? $_POST["reTimeToWhen"] : ''?>" />
                <br>
                <input type="submit" value='Reserve' name='makeReservation'>
    </form>
    <?php 
    global $avaible;
    if($avaible) {
            echo "Reservation Num is : $string_info";
    } else  {
            echo "No Avaible Car Here!";
    }
    ?>
    </div>
    
    <br>
    <hr />
    <br>

    <div>
    <h2>Rent Car</h2> <br>
    <form method = "POST" action = "./SCRRental.php">
        <button class="button" type="submit" name='showReservations'>Show Reservations</button>
        <?php 
            global $db_conn;
            if ($db_conn) {
                if (array_key_exists('showReservations', $_POST)){
                    $result = executePlainSQL("select *
                    from reservation
                    order by fromDateAndTime");
                    $columnNames = array("confNum","vtname","dlicense","fromDateAndTime","toDateAndTime");
                    printTable($result, $columnNames);
                }
                OCILogoff($db_conn);
            } else {
                echo "cannot connect";
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
            }
        ?>
    </form>
    <br>
    <form method = "POST" action = "./SCRRental.php">
                <label for="vlicensevalue"><b>Vlicense:</b></label>
                <input type="text" name="vlicense" placeholder="Vin..." value="<?php echo isset($_POST["vlicense"]) ? $_POST["vlicense"] : ''?>" />
                <br>
                <label for="dlicensevalue"><b>Dlicense:</b></label>
                <input type="text"  name="rentdlicense" placeholder="Driver License #..." value="<?php echo isset($_POST["rentdlicense"]) ? $_POST["rentdlicense"] : ''?>" />
                <br>
                <label for="fromDateAndTimevalue"><b>From Date and Time:</b></label>
                <input type="text"  name="rentfromDateAndTime" placeholder="From..." value="<?php echo isset($_POST["rentfromDateAndTime"]) ? $_POST["rentfromDateAndTime"] : ''?>" />
                <br>
                <label for="toDateAndTimevalue"><b>To Date and Time:</b></label>
                <input type="text"  name="renttoDateAndTime" placeholder="To..." value="<?php echo isset($_POST["renttoDateAndTime"]) ? $_POST["renttoDateAndTime"] : ''?>" />
                <br>
                <label for="odometervalue"><b>Odometer:</b></label>
                <input type="text"  name="rentodometer" placeholder="Odometer..." value="<?php echo isset($_POST["rentodometer"]) ? $_POST["rentodometer"] : ''?>" />
                <br>
                <label for="cardNamevalue"><b>Card Holder Name:</b></label>
                <input type="text"  name="rentcardName" placeholder="Card Holder Name..." value="<?php echo isset($_POST["rentcardName"]) ? $_POST["rentcardName"] : ''?>" />
                <br>
		        <label for="cardNamevalue"><b>CardNo:</b></label>
                <input type="text"  name="rentcardNo" placeholder="Card No..." value="<?php echo isset($_POST["rentcardNo"]) ? $_POST["rentcardNo"] : ''?>" />
                <br>
                <label for="ExpDatevalue"><b>Expire Date:</b></label>
                <input type="text"  name="rentExpDate" placeholder="Expire..." value="<?php echo isset($_POST["rentExpDate"]) ? $_POST["rentExpDate"] : ''?>" />
                <br>
                <label for="confNovalue"><b>Confirmation Number:</b></label>
                <input type="text"  name="rentconfNo" placeholder="Confirm Num..." value="<?php echo isset($_POST["rentconfNo"]) ? $_POST["rentconfNo"] : ''?>" />
                <br>
                <input type="submit" value="Rent" name='rentCar'>
    </form>
    </div>
    <br>
    </body>
</html>