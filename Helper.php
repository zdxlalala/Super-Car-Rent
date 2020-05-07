<html>
<head>
    <style>
        .table{
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        .table td, .table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table tr:nth-child(even){background-color: #f2f2f2;}

        .table tr:hover {background-color: #ddd;}

        .table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>

<?php
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = OCILogon("ora_zdx939", "a65890758", "dbhost.students.cs.ubc.ca:1522/stu");
    function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;
    
        $statement = OCIParse($db_conn, $cmdstr); 
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
    
        if (!$statement) {
            echo "<script>alert('Cannot parse the following command: " . $cmdstr . "')</script>";
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            $string = htmlentities($e['message']);
            echo "<script>alert('$string')</script>";
            $success = False;
        }
    
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<script>alert('Cannot execute the following command: " . $cmdstr . "')</script>";
            // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            $string = htmlentities($e['message']);
            echo "<script>alert('$string')</script>";
            $success = False;
        }
    
        return $statement;
    }
     
    function printTable($resultFromSQL, $namesOfColumnsArray) {
        echo "<br>out put for the seaching<br>";
        
        echo "<table  class='table'>";
        echo "<tr>";
        // iterate through the array and print the string contents
        foreach ($namesOfColumnsArray as $name) {
            echo "<th>$name</th>";
        }
        echo "</tr>";
        echo "<br>";
        while ($row = OCI_Fetch_Array($resultFromSQL, OCI_BOTH)) {
            echo "<tr>";
            $string = "";
    
            for ($i = 0; $i < sizeof($namesOfColumnsArray); $i++) {
                $string .= "<td>" . $row["$i"] . "</td>";
            }
    
            echo $string;
            echo "</tr>";
        }
        echo "</table>";
    }
    $string_info = "";
    $string_rent = "";
    $avaible = false;
    $exist = false;
    
    function findExistCustomer($dlicense) {
        global $exist;
        $result = executePlainSQL("select count(*)
        from customer
        where dlicense = '".$dlicense."'");
        $var = 0;
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $string = "";
                $string = $row["COUNT(*)"];
                $var = $string;
        }
        if($var == 1){
            $exist = true;
        }
        return $exist;
    }
    
    function findAvaibleCar($name,$fromTime,$totime) {
        global $avaible;
        $avaible = true;
        $result = executePlainSQL("select count(*)
        from vehicle 
        where vtname = '".$name."'
        and status = 'Available'
        and vtname not in (select vtname from reservation 
        where ((to_date('".$fromTime."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime) 
        or (to_date('".$totime."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime)))");
        $var = 0;
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $string = "";
                $string = $row["COUNT(*)"];
                $var = $string;
        }
        if($var == 0){
            $avaible = false;
        }
        return $avaible;
    }
    function findTImeAvailbe($fromDateAndTime,$toDateAndTime,$confNo,$vlicense){
        
        global $avaible;
        $avaible = false;
        if ($confNo == ''){
    
            $vt = executePlainSQL("select vtname
            from vehicle
            where vlicense = '".$vlicense."'");
            $row = OCI_Fetch_Array($vt, OCI_BOTH);
            $tname = $row[0];
    
            $numr = executePlainSQL("select count(*)
            from vehicle
            where status = 'Available'
            and vtname = '".$tname."'
            and vtname not in (select vtname from reservation 
            where ((to_date('".$fromDateAndTime."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime) 
            or (to_date('".$toDateAndTime."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime)))");
    
            $var1 = 0;
            while ($row = OCI_Fetch_Array($numr, OCI_BOTH)) {
                $string1 = "";
                $string1 = $row["COUNT(*)"];
                $var1 = $string1;
            }
            
            $result = executePlainSQL("select count(*)
            from rental
            where vlicense = '".$vlicense."'");
            $var2 = 0;
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $string2 = "";
                $string2 = $row["COUNT(*)"];
                $var2 = $string2;
            }
            if($var1 > 0 && $var2 == 0){
                $avaible = true;
            }
        }
        else{

            $result = executePlainSQL("select count(*)
            from reservation 
            where confNo = '".$confNo."'");
            
            $var = 0;
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $string = "";
                $string = $row["COUNT(*)"];
                $var = $string;
            }
            $result1 = executePlainSQL("select count(*)
            from rental
            where vlicense = '".$vlicense."'");
            $var2 = 0;
            while ($row = OCI_Fetch_Array($result1, OCI_BOTH)) {
                $string2 = "";
                $string2 = $row["COUNT(*)"];
                $var2 = $string2;
            }
            if($var == 1 && $var2 == 0){
                $avaible = true;
            }
        }
        return $avaible;
    }
    function findExistRental($rid){
        global $exist;
        $exist = false;
        
        $returned = executePlainSQL("select count(*)
        from return
        where rid = '".$rid."'");
        $var1 = 0;
        while ($row = OCI_Fetch_Array($returned, OCI_BOTH)) {
            $string1 = "";
            $string1 = $row["COUNT(*)"];
            $var1 = $string1;
        }
    
        $result = executePlainSQL("select count(*)
        from rental
        where rid = '".$rid."'");
        $var = 0;
        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            $string = "";
            $string = $row["COUNT(*)"];
            $var = $string;
        }
        if($var == 1 && $var1 == 0){
            $exist = true;
        }
        return $exist;
    
    }
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
            $columnNames = array("Car number");
        } elseif (array_key_exists('showMore', $_POST)) {
            $result = executePlainSQL("select *
             from vehicle where status = 'Available'
             and (vtname = '".$_POST["cartype"]."' or '".$_POST["cartype"]."' is null)
             and (location = '".$_POST["locationName"]."'or '".$_POST["locationName"]."' is null)
             and vtname not in (select vtname from reservation 
             where ((to_date('".$_POST["timeFromWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime) 
             or (to_date('".$_POST["timeToWhen"]."','yyyy-mm-dd hh24:mi') between fromDateAndTime and toDateAndTime)))");
             $columnNames = array("vlicense","make","model","year","color","odometer","status","vtname","location","city");
       } elseif (array_key_exists('makeReservation', $_POST)) {
            $vtname = $_POST["vtname"];
            $dlicense = $_POST["dlicense"];
            $fromTime = $_POST["reTimeFromWhen"];
            $totime = $_POST["reTimeToWhen"]; 
            if(findExistCustomer($dlicense)) {
    
                if(findAvaibleCar( $vtname, $fromTime,$totime)) {
                    $var = rand(10000,99999);
                        $info =  "{$var}{$_POST['dlicense']}{$_POST['vtname']}";
                        $string_info  = $info;
                    
                    executePlainSQL("insert into reservation values (
                        '$info',
                        '".$_POST["vtname"]."',
                        '".$_POST["dlicense"]."',
                        to_date('".$_POST["reTimeFromWhen"]."','yyyy-mm-dd hh24:mi'),
                        to_date('".$_POST["reTimeToWhen"]."','yyyy-mm-dd hh24:mi'))" );
    
                } else 
                    echo "<script>alert('No avalibale car here!')</script>";
            }else {
                echo "<script>alert('No exist customer record. Need to sign up first!')</script>";
            }
        } elseif (array_key_exists('showReservations', $_POST)){
            $result = executePlainSQL("select *
            from reservation
            order by fromDateAndTime");
            $columnNames = array("confNum","vtname","dlicense","fromDateAndTime","toDateAndTime");
    
        } elseif (array_key_exists('rentCar', $_POST)) {
            $var = rand(10000,99999);
            $rid = "{$var}";
            $fromDateAndTime = $_POST["rentfromDateAndTime"];
            $toDateAndTime = $_POST["renttoDateAndTime"];
            $confNo = $_POST["rentconfNo"];
            $vlicense = $_POST["vlicense"];
            
    
            if(findTImeAvailbe($fromDateAndTime,$toDateAndTime,$confNo,$vlicense)) {
                executePlainSQL("insert into rental values (
                    '$rid',
                    '".$_POST["vlicense"]."',
                    '".$_POST["rentdlicense"]."',
                    to_date('".$_POST["rentfromDateAndTime"]."','yyyy-mm-dd hh24:mi'),
                    to_date('".$_POST["renttoDateAndTime"]."','yyyy-mm-dd hh24:mi'),
                    '".$_POST["rentodometer"]."',
                    '".$_POST["rentcardName"]."',
                    '".$_POST["rentcardNo"]."',
                    to_date('".$_POST["rentExpDate"]."','yyyy-mm-dd hh24:mi'),
                    '".$_POST["rentconfNo"]."')" );
                executePlainSQL("update vehicle set status='Rented' where vlicense='" . $vlicense . "'");
            } else 
                echo "<script>alert('no avalibale car here')</script>";
    
        } elseif (array_key_exists('showRentals', $_POST)){
            $result = executePlainSQL("select *
                        from rental
                        order by fromDateAndTime");
                        $columnNames = array("rid","vlicense","dlicense","fromDateAndTime","toDateAndTime","odometer","cardName","cardNo","ExpDate","confNo");
    
        }elseif (array_key_exists('returnCar', $_POST)) {
            $rid = $_POST["rid"];
            $rDateAndTime = $_POST["rDateAndTime"];
            $odometer = $_POST["odometer"];
            $fulltank = $_POST["fulltank"];
            $value = $_POST["value"]; 
            if(findExistRental($rid)) {
    
                $result = executePlainSQL("select vlicense
                from rental
                where rid = '".$rid."'");
                $row = OCI_Fetch_Array($result, OCI_BOTH);
                $vlicense = $row[0];
                    
                executePlainSQL("insert into return values (
                    '$rid',
                    to_date('$rDateAndTime','yyyy-mm-dd hh24:mi'),
                    '$odometer',
                    '$fulltank',
                    '$value')");
    
                executePlainSQL("update vehicle set status='Available' where vlicense='" . $vlicense . "'");
                executePlainSQL("delete from rental where vlicense='" . $vlicense . "'");
    
                //$receipt = executePlainSQL("select return.rid, return.value, return.rDateAndTime, rental.vlicense, rental.dlicense, rental.fromDateAndTime, rental.confNo
                //from return, rental
                //where return.rid = rental.rid");
    
                //$columnNames = array("Return ID","Return Date","Odometer","Full Tank","Value");
                //executePlainSQL("delete from rental where rid = '".$rid."'");        
            }else {
                echo "<script>alert('No exist rental record! or The Car is already returned!')</script>";
            }
        }elseif(array_key_exists('showRecipt', $_POST)) {
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
                }
    
        }else { // equip champion button
            
        }
    
        //Commit to save changes...
        OCICommit($db_conn);
        OCILogoff($db_conn);
    } else {
        echo "cannot connect";
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
    }
?>
</html>