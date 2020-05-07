<html>
    <head>
        <title>SignUp</title>
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
            $page = 'SignUp';
            include 'Header.php';
        ?>
        <div>
        <h2>Sign Up</h2>
        <form method="POST" action="SignUp.php"> <!--refresh page when submitted-->
            <input type="hidden" id="signupQueryRequest" name="signupQueryRequest">
            Driver License: <input type="text" name="dlicense" placeholder="Driver License #..." value = "<?php echo isset($_POST["dlicense"]) ? $_POST["dlicense"] : ''?>" required> <br /><br />
            Name: <input type="text" name="name" placeholder="Name..." value = "<?php echo isset($_POST["name"]) ? $_POST["name"] : ''?>" required> <br /><br />
            Address: <input type="text" name="addrease" placeholder="Address..." value = "<?php echo isset($_POST["addrease"]) ? $_POST["addrease"] : ''?>"> <br /><br />
            Phone Number: <input type="text" name="cellphone" placeholder="Phone Num..." value = "<?php echo isset($_POST["cellphone"]) ? $_POST["cellphone"] : ''?>"> <br /><br />

            <input type="submit" value="Sign Up" name="signupSubmit"></p>
        </form>

        <br>
        <form method="POST" action="SCRRental.php">
            <button class="button" type="submit" name="back">Back</button>
        </form>

        </div>

        <hr />
        <h2>Count the Tuples in Custom</h2>
        <form method="GET" action="SignUp.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <button class="button" type="submit"  name="countTuples">Count</button></p>
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $db_conn = NULL;
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr); 
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection. 
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table customer:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example, 
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_zdx939", "a65890758", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;
            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['dlicense'],
                ":bind2" => $_POST['name'],
                ":bind3" => $_POST['address'],
                ":bind4" => $_POST['cellphone']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into customer values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM customer");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in customer: " . $row[0] . "<br>";
            }
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()){
                if (array_key_exists('signupQueryRequest', $_POST)) {
                    handleInsertRequest();
            }
        }
            disconnectFromDB();
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()){
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
            }
        }
            disconnectFromDB();
        }

		if (isset($_POST['signupSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>