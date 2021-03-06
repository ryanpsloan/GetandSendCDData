<?php
include_once ("dbConnect.php");
if(isset($_POST['selectedRows'])){
    try{
        $lines = array();
        $dataArray = $_POST['selectedRows'];
        //var_dump($dataArray);
        //prepare the lines of the job file ASCII

        foreach($dataArray as $arr){

                    $lines[] = array(
                        "JobID = " . $arr['clientId'],
                        "ClientID = CLIENT",
                        "Data = C:\\PTBurnData\\FileTrees\\" . $arr['clientId'],
                        "CloseDisc = YES",
                        "VerifyDisc = YES",
                        "MergeField = " . $arr['clientId'] . " - " . $arr["clientName"],
                        "PrintLabel = C:\\PTBurnData\\LabelData\\CDlabelimage.std",
                        /***
                         * Note that the print file specified within the JRQ must be a SureThing file, and it must have been designed with a Merge File specified.
                         * Note that the fields should be specified in the correct order to match the SureThing design.
                         *
                         *
                         *
                         *
                         */
                    );




        }
        //var_dump($lines);
        //create the file
        echo "<h3>Job Files Created</h3>";
        $today = new DateTime("NOW");
        $ftp_host = "Client";
        $ftp_user_name = "user";
        $ftp_user_pass = "pass";
        $connect_it = ftp_connect($ftp_host);
        $login_result = ftp_login($connect_it, $ftp_user_name, $ftp_user_pass);
        foreach($lines as $arr) {
            $fileName = "ftp/jobfile ". substr($arr[0],8). " " .$today->format("M-d-Y-H-s").".txt";
            if (($myfile = fopen($fileName, "w")) === false) { //open the file
                //if unable to open throw exception
                throw new RuntimeException("Could Not Open File Location on Server.");
            }
            foreach($arr as $line){
                fwrite($myfile, $line . PHP_EOL);
            }
            fclose($myfile);
            $remote_file = substr($arr[0],8) . " jobfile.jrq";
            $local_file = $fileName;
            //var_dump($remote_file);
            if(ftp_put($connect_it, $remote_file, $local_file, FTP_BINARY)){
                echo "<p style='background-color:lightgreen'>" . $arr[0] . " Successful Transfer </p>";
            } else{
                echo "<p style='background-color:red'>" . $arr[0] . " Unsuccessful Transfer </p>";
            }

        }
        ftp_close($connect_it);

        /*$mysqli = MysqliConfiguration::getMysqli();
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        foreach($dataArray as $arr) {
            $clientId = $arr['clientId'];
            $query     = "DELETE FROM clients WHERE clientId = ?";
            $statement = $mysqli->prepare($query);
            if($statement === false) {
                throw(new mysqli_sql_exception("Unable to prepare statement"));
            }
            $wasClean = $statement->bind_param("s", $clientId);
            if($wasClean === false) {
                throw(new mysqli_sql_exception("Unable to bind parameters"));
            }
            if($statement->execute() === false) {
                throw(new mysqli_sql_exception("Unable to execute mySQL statement"));
            }
        }*/


    }catch(Exception $e){

        echo "<h3>". $e->getMessage() . "</h3>";

    }

}






?>
