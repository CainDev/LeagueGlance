<?php
    class league_api {
        function __construct(){
            // https://developer.riotgames.com/
            $this->api_key = "RIOT API KEY";
        }

        private $api_key;
        private $summoner_information;
        public $currentSummoners = [];
    

        function StatusCode($status_code, $customNote){
            $response = [];

            if ($customNote = ""){
                $customNote = "None Supplied.";
            }

            switch($status_code){
                case 200:
                    $response['code'] = $status_code;
                    $response['success'] = true;
                    $response['note'] = "Ok.";
                    $response['customnote'] = $customNote;
                    return $response;
                case 400:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "League's API failed the request.";
                    $response['customnote'] = $customNote;
                    return $response;
                case 403:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "Something is wrong with LeagueGlance's API Key. Please contact an Admin.";
                    $response['customnote'] = $customNote;
                    return $response;
                case 404:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "Data not found.";
                    $response['customnote'] = $customNote;
                    return $response;
                case 429:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "LeagueGlance's API is being rate limited. Try again in 10 seconds...";
                    $response['customnote'] = $customNote;
                    // Rate Limit
                    return $response;
                case 503:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "League of Legend's API Service is unavailable.";
                    $response['customnote'] = $customNote;
                    return $response;
                default:
                    $response['code'] = $status_code;
                    $response['success'] = false;
                    $response['note'] = "Unknown error, contact site Admin.";
                    $response['customnote'] = $customNote;
                    return $response;
            }
        }

        function ValidateRegion($region){
            // LA1 = LAN
            // LA2 = LAS
            switch($region){
                case "EUW":
                    return "euw1";
                case "NA":
                    return "na1";
                case "EUNE":
                    return "eun1";
                case "LAS":
                    return "la2";
                case "OCE":
                    return "oc1";
                case "TR":
                    return "tr1";
                case "RU":
                    return "ru";
                case "JP":
                    return "jp1";
                case "BR":
                    return "br1";
                case "KR":
                    return "kr";
                case "LAN":
                    return "la1";
                default:
                    return false;
            }
        }

        function GrabAccountData($summoner_name, $region){
            if ($region == "RU"){
                if(strlen($summoner_name) < 3 || strlen($summoner_name) > 32){
                    return false;
                }
            } else if ($region == "KR") {
                if(strlen($summoner_name) < 3 || strlen($summoner_name) > 48){
                    return false;
                }
            } else {
                if(strlen($summoner_name) < 3 || strlen($summoner_name) > 16){
                    return false;
                }
            }
            
            
            $ids_request_url = "https://". $this->ValidateRegion($region) . ".api.riotgames.com/lol/summoner/v4/summoners/by-name/" . urlencode($summoner_name) . "?api_key=" . $this->api_key;
            $data = $this->APIRequest($ids_request_url);
            $status_code = $this->StatusCode($data[1], "GrabAccountData();");

            if($status_code['success']){
                $decoded_json = json_decode($data[0], true);

                $summoner = new summoner();
                $summoner->summonerID = $decoded_json['id'];
                $summoner->accountID = $decoded_json['accountId'];
                $summoner->iconID = $decoded_json['profileIconId'];
                $summoner->summonerName = $decoded_json['name'];
                $summoner->summonerLevel = $decoded_json['summonerLevel'];
                $summoner->revisionDate = $decoded_json['revisionDate'] / 1000;
                $summoner->puuID = $decoded_json['puuid'];
                $summoner->region = $region;      
                array_push($this->currentSummoners, $summoner);
                return $status_code;
            } else {
                return $status_code;
            }   
        }

        function GrabLastMatch($account_id, $region){
            $match_request_url = "https://". $this->ValidateRegion($region) . ".api.riotgames.com/lol/match/v4/matchlists/by-account/"
                    . urlencode($account_id)
                    . "?endIndex=1&beginIndex=0&api_key="
                    . $this->api_key;


            $data = $this->APIRequest($match_request_url);
            $status_code = $this->StatusCode($data[1], "GrabLastMatch();");
            if($status_code['success']){
                $decoded_json = json_decode($data[0], true);
                
                $this->currentSummoners[0]->lastMatchData['platformId'] = $decoded_json['matches'][0]['platformId'];
                $this->currentSummoners[0]->lastMatchData['gameId'] = $decoded_json['matches'][0]['gameId'];
                $this->currentSummoners[0]->lastMatchData['champion'] = $decoded_json['matches'][0]['champion'];
                $this->currentSummoners[0]->lastMatchData['queue'] = $decoded_json['matches'][0]['queue'];
                $this->currentSummoners[0]->lastMatchData['season'] = $decoded_json['matches'][0]['season'];
                $this->currentSummoners[0]->lastMatchData['timestamp'] = $decoded_json['matches'][0]['timestamp'] / 1000;
                $this->currentSummoners[0]->lastMatchData['role'] = $decoded_json['matches'][0]['role'];
                $this->currentSummoners[0]->lastMatchData['lane'] = $decoded_json['matches'][0]['lane'];

                return $status_code;
            } else {
                return $status_code;
            } 
        }
        function CalculateLastMatch($timestamp){
            return date("d-m-Y", $timestamp);
        }

        function CalculateDaysSince($timestamp){
            return ceil($timestamp / 86400);
        }
        
        function CalculateNameExpiry($summonerLevel, $lastMatchDate){
            $protectionMonths = 6;

            if($summonerLevel < 6){
                $protectionMonths = 6;
            } else {

                // Level 30 for Max Immunity
                $protectionMonths = $summonerLevel;

                if($protectionMonths > 30) {
                    $protectionMonths = 30;
                }
            }

            $unixStamp = $protectionMonths * 2629743;
            $accountExpiry = $lastMatchDate + $unixStamp;
            return $accountExpiry;
        }

        function APIRequest($url){
            $reponseData = [];
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            
            // Returns the actual body.
            $responseData[0] = curl_exec($curlHandle); 

            // Returns Int Status Code
            $responseData[1] = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

            curl_close($curlHandle);
            return $responseData; 
        }
    }

    class summoner {
        public $summonerID = "";
        public $accountID = "";
        public $iconID = 19;
        public $puuID = "";
        public $summonerName = "";
        public $summonerLevel = 1;
        public $revisionDate = 0;
        public $lastMatchData = [];
        public $region = "";
        public $protectionMonths = 6;
    }
?>