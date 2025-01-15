<?php
include 'assets/php/util.php';
$searched = false;

if (isset($_POST['summoner-submit'])) {
  $captcha = $_POST['g-recaptcha-response'];
  $secretKey = "USE YOUR OWN SECRET KEY";
  $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($captcha);
  $response = file_get_contents($url);
  $responseKeys = json_decode($response, true);

  if ($responseKeys["success"] == true) {
    $postResponse = "Captcha Complete.";
  } else {
    $postResponse = "Please complete the reCaptcha.";
  }
}
?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8">
  <meta name="description" content="Look up League of Legends names in all regions with our free-to-use tool.">
  <meta name="author" content="Cain 😎">
  <meta name="viewport" content="width=device-width, initial-scale=2.0">
  <meta property="og:image" content="/assets/LeagueGlanceLogo.png" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <title>LoL Name Look-up</title>
  <link rel="stylesheet" href="assets/css/styles.css?v=1.0">
  <link rel="stylesheet" href="assets/css/modal.css?v=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/fc55f6e00b.js" crossorigin="anonymous"></script>

</head>

<body>
  <div class="modal" id="modal">
    <div class="report-modal-content">
      <div class="report-modal-header">
        <div class="report-modal-close"><i class="fas fa-times" onclick="return closeModal(1)"></i></div>
        <h2>Report</h2>
      </div>

      <div class="report-modal-body">
        <p>If you have any issues or bugs you'd like to report. You can email me here: report@leagueglance.com</p>
      </div>
    </div>
  </div>

  <div class="panel" id="panel">
    <div class="header">
      <div class="headerText">
        LeagueGlance
      </div>

      <div class="links">
        <a href="http://leagueglance.com/">Home</a> -
        <a href="https://shoppy.gg/@LeagueGlance" target="_blank" rel="noreferrer">Buy a Name</a> -
        <a href="about.html">About Us</a>
      </div>
    </div>

    <hr class="break">
    <form action="index2-no-db.php" method="POST" name="summoner-search" id="summoner-search"
      onsubmit="return formValidation()">
      <div id="summoner-search-inputs">
        <input type="text" id="summoner-name" name="summoner-name" placeholder="Enter Summoner Name" maxlength="16">
        <select name="region-dropdown" id="region-dropdown">
          <option value="EUW">EUW</option>
          <option value="NA">NA</option>
          <option value="EUNE">EUNE</option>
          <option value="LAS">LAS</option>
          <option value="OCE">OCE</option>
          <option value="TR">TR</option>
          <option value="RU">RU</option>
          <option value="JP">JP</option>
          <option value="BR">BR</option>
          <option value="KR">KR</option>
          <option value="LAN">LAN</option>
        </select>
      </div>
      <br>
      <button type="submit" id="summoner-submit" name="summoner-submit">
        Search
      </button>
      <br><br>
      <center>
        <div class="g-recaptcha" data-sitekey="6Lc5OaAaAAAAANtnp4vHYysJuqqLY844E2CAgD3e"></div>
        <small>reCaptcha is set to Easiest setting.</small>
      </center>
    </form>

    <?php
    if (isset($_POST['summoner-submit']) && $responseKeys["success"] == true) {
      include 'assets/php/league_lookup.php';
      $date = new DateTime();
      $leagueApi = new league_api();

      $searched = true;
      $summonerName = htmlspecialchars($_POST['summoner-name'], ENT_QUOTES, 'UTF-8');
      $summonerRegion = htmlspecialchars($_POST['region-dropdown'], ENT_QUOTES, 'UTF-8');

      $currUnixTime = $date->getTimestamp();
      $purchaseData = "";

      if (empty($purchaseData) || $purchaseData['sold'] == 1) {
        $accountData = $leagueApi->GrabAccountData($summonerName, $summonerRegion);

        if ($accountData['success']) {
          $matchData = $leagueApi->GrabLastMatch($leagueApi->currentSummoners[0]->accountID, $summonerRegion);

          if ($matchData['success']) {

            $daysSinceLastMatch = $currUnixTime - $leagueApi->currentSummoners[0]->lastMatchData['timestamp'];

            // ----
            $totalDays = $leagueApi->CalculateDaysSince($daysSinceLastMatch);
            $totalDaysString = "";
            $lastMatch = $leagueApi->CalculateLastMatch($leagueApi->currentSummoners[0]->lastMatchData['timestamp']);
            $lastMatchTimestamp = $leagueApi->currentSummoners[0]->lastMatchData['timestamp'];

            if ($totalDays == 1) {
              $totalDaysString = "(1 Day ago)";
            } else {
              $totalDaysString = "({$totalDays} Days ago)";
            }
            // ----
            $accountLastActive = $leagueApi->currentSummoners[0]->revisionDate;

            if ($accountLastActive < $lastMatchTimestamp) {
              $accountLastActive = $lastMatchTimestamp;
            }

            $nameExpiryDateUnix = $leagueApi->CalculateNameExpiry($leagueApi->currentSummoners[0]->summonerLevel, $accountLastActive);
            $daysUntilExpiry = $leagueApi->CalculateDaysSince($nameExpiryDateUnix - $currUnixTime);
            $accountLastActiveDate = date("d-m-Y", $leagueApi->CalculateNameExpiry($leagueApi->currentSummoners[0]->summonerLevel, $accountLastActive));
            $daysUntilString = "({$daysUntilExpiry} Days)";
            // ----
    
            $opggLink = '<a href="https://' . $summonerRegion . '.op.gg/summoner/userName=' . urlencode($leagueApi->currentSummoners[0]->summonerName) . '" target="_blank" rel="noreferrer">OP.GG</a>';
            $uggLink = '<a href="https://u.gg/lol/profile/' . $leagueApi->ValidateRegion($summonerRegion) . '/' . $leagueApi->currentSummoners[0]->summonerName . '/overview" target="_blank" rel="noreferrer">U.GG</a>';

            echo '<br>';
            echo '<hr class="break-two">';
            echo '<div class="summoner-result">';
            echo '<div class="summoner-result-header">';
            echo '<br>';
            echo '<h2 class="summoner-result-name">' . $leagueApi->currentSummoners[0]->summonerName . '</h2>';
            echo "<img src=\"https://ddragon.leagueoflegends.com/cdn/11.6.1/img/profileicon/" . $leagueApi->currentSummoners[0]->iconID . ".png\" class=\"summoner-result-img\">";
            echo '</div>';
            echo '<div class="summoner-result-content">';
            echo '<p><b>Summoner Level:</b> ' . $leagueApi->currentSummoners[0]->summonerLevel . '</p>';
            echo '<p><b>Last Match: </b>' . $lastMatch . " " . $totalDaysString . '</p>';
            echo '<p><b>Name Clean-up Date: </b>' . $accountLastActiveDate . " " . $daysUntilString . '</p>';
            echo '<p><b>Links: </b>' . $opggLink . " / " . $uggLink . '</p>';
            echo '</div>';
            echo '</div>';
          } else if ($matchData['code'] == 404) {
            $accountLastActive = $leagueApi->currentSummoners[0]->revisionDate;
            $accountLastActiveDate = date("d-m-Y", $leagueApi->CalculateNameExpiry($leagueApi->currentSummoners[0]->summonerLevel, $accountLastActive));
            $nameExpiryDateUnix = $leagueApi->CalculateNameExpiry($leagueApi->currentSummoners[0]->summonerLevel, $accountLastActive);
            $daysUntilExpiry = $leagueApi->CalculateDaysSince($nameExpiryDateUnix - $currUnixTime);


            if ($daysUntilExpiry < 0) {
              $randomLink = "assets/avatars/good/" . strval(rand(1, 11)) . ".png";
              echo '<br>';
              echo '<hr class="break-two">';
              echo '<div class="summoner-result">';
              echo '<div class="summoner-result-header">';
              echo '<br>';
              echo '<h2 class="summoner-result-name-success">';
              echo $leagueApi->currentSummoners[0]->summonerName . ' is Available!' . '<br>';
              echo 'Cleaned up ' . abs($daysUntilExpiry) . ' days ago.<br>';
              echo 'Register ' . "<a href=\"https://signup." . strtolower($summonerRegion) . ".leagueoflegends.com/\" target=\"_blank\">[HERE]</a>";
              echo '</h2>';
              echo '<img src="' . $randomLink . '" class="summoner-result-img-success">';
              echo '</div>';
              echo '</div>';
            } else {
              $randomLink = "assets/avatars/bad/" . strval(rand(1, 8)) . ".png";
              echo '<br>';
              echo '<hr class="break-two">';
              echo '<div class="summoner-result">';
              echo '<div class="summoner-result-header">';
              echo '<br>';
              echo '<h2 class="summoner-result-name-error">';
              echo $leagueApi->currentSummoners[0]->summonerName . " is close!" . "<br>";
              echo $daysUntilExpiry . ' Days to go...' . '<br>';
              echo '</h2>';
              echo '<img src="' . $randomLink . '" class="summoner-result-img-error">';
              echo '</div>';
              echo '</div>';
            }
          } else {
            $randomLink = "assets/avatars/bad/" . strval(rand(1, 8)) . ".png";
            echo '<br>';
            echo '<hr class="break-two">';
            echo '<div class="summoner-result">';
            echo '<div class="summoner-result-header">';
            echo '<br>';
            echo '<h2 class="summoner-result-name-error">';
            echo "Error! Status Code: {$matchData['code']}, Note: {$matchData['note']}";
            echo '</h2>';
            echo '<img src="' . $randomLink . '" class="summoner-result-img-error">';
            echo '</div>';
            echo '</div>';
          }

        } else if ($accountData['code'] == 404) {
          $randomLink = "assets/avatars/good/" . strval(rand(1, 11)) . ".png";
          echo '<br>';
          echo '<hr class="break-two">';
          echo '<div class="summoner-result">';
          echo '<div class="summoner-result-header">';
          echo '<br>';
          echo '<h2 class="summoner-result-name-success">';
          echo $summonerName . ' is Available!' . '<br>';
          echo 'Register ' . "<a href=\"https://signup." . strtolower($summonerRegion) . ".leagueoflegends.com/\" target=\"_blank\">[HERE]</a>";
          echo '</h2>';
          echo '<img src="' . $randomLink . '" class="summoner-result-img-success">';
          echo '</div>';
          echo '</div>';
        } else {
          $randomLink = "assets/avatars/bad/" . strval(rand(1, 8)) . ".png";
          echo '<br>';
          echo '<hr class="break-two">';
          echo '<div class="summoner-result">';
          echo '<div class="summoner-result-header">';
          echo '<br>';
          echo '<h2 class="summoner-result-name-error">';
          echo "Error! Status Code: {$accountData['code']}, Note: {$accountData['note']}";
          echo '</h2>';
          echo '<img src="' . $randomLink . '" class="summoner-result-img-error">';
          echo '</div>';
          echo '</div>';
        }
      } else {
        echo '<script src="https://shoppy.gg/api/embed.js"></script>';
        $searched = true;
        $randomLink = "assets/avatars/bad/" . strval(rand(1, 8)) . ".png";
        echo '<br>';
        echo '<hr class="break-two">';
        echo '<div class="summoner-result">';
        echo '<div class="summoner-result-header">';
        echo '<br>';
        echo '<h2 class="summoner-result-name-success-pur">';
        echo $summonerName . ' is Available but...<br><br>';
        $daysTaken = $leagueApi->CalculateDaysSince($currUnixTime - $purchaseData['registeredUnix']);
        echo 'We liked this name so much we claimed it ' . $daysTaken . ' days ago!<br><br>';
        echo 'If you really want this name and want to support LeagueGlance.com' . '<br><br>';
        echo 'You can purchase the name for a cheap price!<br>';
        echo '<button id="pay-button" data-shoppy-product="' . $purchaseData['link'] . '">Purchase</button> <button id="pay-button" onclick="window.open(\'https://shoppy.gg/@LeagueGlance/groups/' . ReturnShoppyGroup($summonerRegion) . '\',\'_blank\');" type="button">Other Names</button>';
        echo '<div id="pay-footer-notice">(PayPal, Stripe or Cryptos Accepted!)</div>';
        echo '</h2>';
        //echo '<img src="' . $randomLink . '" class="summoner-result-img-success">';
        echo '</div>';
        echo '</div>';
      }
    } else if (isset($_POST['summoner-submit'])) {
      $searched = true;
      $randomLink = "assets/avatars/bad/" . strval(rand(1, 8)) . ".png";
      echo '<br>';
      echo '<hr class="break-two">';
      echo '<div class="summoner-result">';
      echo '<div class="summoner-result-header">';
      echo '<br>';
      echo '<h2 class="summoner-result-name-error">';
      echo "Please complete the captcha.";
      echo '</h2>';
      echo '<img src="' . $randomLink . '" class="summoner-result-img-error">';
      echo '</div>';
      echo '</div>';
    }
    ?>

    <?php
    if ($searched == false) {
      echo '<p class="leagueglance-desc">LeagueGlance.com is a free tool that calculates how many days until your desired summoner name will become available. To get started try searching for your dream name!</p>';
    }
    ?>

    <div class="panel-footer">
      <p>Made with 💓 by <b>Cain</b></p>
    </div>
  </div>

  <div class="footer-notice">All backgrounds, data and avatars used are property of <br>League of Legends © <a
      href="https://www.riotgames.com/en">Riot
      Games</a>,
    Inc.
  </div>
  <div class="footer-icons">
    <!-- <i class="fas fa-cogs" onclick="return openModal(3)"></i>
    //<i class="fas fa-bug" onclick="return openModal(2)"></i> -->
    <i class="fas fa-flag" onclick="return openModal(1)"></i>
  </div>

  <script src="assets/js/text-validation.js"></script>
  <script src="assets/js/modal.js"></script>
  <script src="assets/js/random.js"></script>
  <script>
    setBackground();
  </script>
</body>

</html>