<html>
    <head>
        <title>Car Return</title>
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
        $page = 'Return';
        include 'Header.php';
        include 'Helper.php';
    ?>
    <div>
    <h2>Return Car</h2> <br>
        <form method = "POST" action = "./CarReturn.php">
        <button class="button" type="submit" name='showRentals'>Show Rentals</button>
        <?php 
            global $db_conn;
            if ($db_conn) {
                if (array_key_exists('showRentals', $_POST)){
                    $result = executePlainSQL("select *
                    from rental
                    order by fromDateAndTime");
                    $columnNames = array("rid","vlicense","dlicense","fromDateAndTime","toDateAndTime","odometer","cardName","cardNo","ExpDate","confNo");
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
    <form method = "POST" action = "./CarReturn.php">
                <label for="rid"><b>Rental ID:</b></label>
                <input type="text" name="rid" placeholder="RID..." value="<?php echo isset($_POST["rid"]) ? $_POST["rid"] : ''?>" />
                <br>
                <label for="rDateAndTime"><b>Return Date:</b></label>
                <input type="text"  name="rDateAndTime" placeholder="Date..." value="<?php echo isset($_POST["rDateAndTime"]) ? $_POST["rDateAndTime"] : ''?>" />
                <br>
                <label for="odometer"><b>Odometer:</b></label>
                <input type="text"  name="odometer" placeholder="Odometer..." value="<?php echo isset($_POST["odometer"]) ? $_POST["odometer"] : ''?>" />
                <br>
                <label for="fulltank"><b>Full Tank:</b></label>
                <input type="text"  name="fulltank"  value="<?php echo isset($_POST["fulltank"]) ? $_POST["fulltank"] : ''?>" />
                <br>
                <label for="value"><b>Value:</b></label>
                <input type="text"  name="value" placeholder="Price..." value="<?php echo isset($_POST["value"]) ? $_POST["value"] : ''?>" />
                <br>
                <input type="submit" value="Return" name='returnCar'>
                <br>
                <button class="button" type="submit" name='showRecipt'>Show Recipt</button>
                
                <?php 
                    global $db_conn;
                    if ($db_conn){
                        if(array_key_exists('showRecipt', $_POST)) {
                            $rid = $_POST["rid"];
                            $rDateAndTime = $_POST["rDateAndTime"];
                            $odometer = $_POST["odometer"];
                            $fulltank = $_POST["fulltank"];
                            $value = $_POST["value"]; 
                            if(findExistRental($rid)) {
                                $receipt = executePlainSQL("select return.rid, return.value, return.rDateAndTime, rental.vlicense, rental.dlicense, rental.fromDateAndTime, rental.confNo
                                from return, rental
                                where return.rid = rental.rid");

                                $columnNames = array("Return ID","Return Date","Odometer","Full Tank","Value");
                                printTable($receipt, $columnNames);
                            }
                        }
                        OCILogoff($db_conn);
                }else{
                    echo "cannot connect";
                    $e = OCI_Error(); // For OCILogon errors pass no handle
                    echo htmlentities($e['message']);
                }
            ?>
    </form>
    </div>
    </body>
</html> 