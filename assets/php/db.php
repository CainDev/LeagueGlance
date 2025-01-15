<?php  
    //error_reporting(0); 
    class databasehandler {
        public $mysqli;

        function __construct() {   
            $this->mysqli = new mysqli("127.0.0.1", "DB USERNAME", "DB PASSWORD", "DB NAME");

            if ($this->mysqli->connect_error) {
                die('Connect Error (' . $this->mysqli->connect_errno . ') '
                        . $this->mysqli->connect_error);
            }
        }

        function SearchName($summoner_name, $region){
            $sql = $this->mysqli->prepare('SELECT * FROM leagjlhp_owned.lg_names WHERE summoner = ? AND region = ? ORDER by "region"');
            $sql ->bind_param("ss", $summoner_name, $region);
            $sql ->execute();
            $result = $sql->get_result();
            $nameInfo = $result->fetch_assoc();

            return $nameInfo;
        }

        function UpdateSearches($summoner_name, $region){
            $sql = $this->mysqli->prepare("UPDATE leagjlhp_owned.lg_names SET searches = searches + 1 WHERE summoner = '$summoner_name' AND region = '$region'");
            $sql ->bind_param("ss", $summoner_name, $region);
            $sql ->execute();
        }

        function RegionDesc(){
            $sql = $this->mysqli->prepare('SELECT * FROM leagjlhp_owned.lg_names ORDER BY region DESC');
            $sql ->execute();
            $result = $sql->get_result();
            $regionAsc = $result->fetch_assoc();

            return $regionAsc;
        }

        function RemoveSold($shoppyLink){
            $sql = $this->mysqli->prepare("UPDATE leagjlhp_owned.lg_names SET sold = 1 WHERE link = '$shoppyLink'");
            $sql ->bind_param("s", $shoppyLink);
            $sql ->execute();
        }

        function __deconsruct(){
            $this->mysqli->close();
        }
    }
?>