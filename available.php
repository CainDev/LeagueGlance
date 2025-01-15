<?php
    include 'assets/php/db.php';
    echo '<script src="https://shoppy.gg/api/embed.js"></script>';
    //$dbHandler = new databasehandler();
    $pages = 0;
    $limit = 10;
?>
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="description" content="Purchase an original Summoner Name from LeagueGlance.com">
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
                <a id="header-link" href="http://leagueglance.com/">Search</a> -
                <a id="header-link" href="purchase">Premium Names</a> -
                <a id="header-link" href="available">Free Names</a> -
                <a id="header-link" href="about">About Us</a> -
                <a id="header-link" href="privacy">Privacy Policy</a>
        </div>
    </div>

    <hr class="break">
    <h2 id="avail-names-header">Now Available!</h2>
    <div class="avail-controls">
        <label id="region-label" for="region-dropdown">Region:</label>
        <select name="region-dropdown" id="region-dropdown-avail">
            <option value="EUW">EUW</option>
            <option value="NA">NA</option>
            <option value="EUNE">EUNE</option>
            <option value="LAS">LAS</option>
            <option value="OCE">OCE</option>
        </select>
        <label id="region-label" for="paid-required">Paid Swap:</label>
        <select name="paid-required" id="region-dropdown-avail">
            <option value="EUW">Yes</option>
            <option value="NA">No</option>
        </select>
    </div>

    <br>

    <table id="key-table">
        <tr>
            <th>Summoner Name</th>
            <th>Region</th>
            <th>Paid Swap Required <img src="assets/suggestion.png" title="Because this name was claimed before, it requires a paid name swap, this costs 13,900 BE or 1300 RP."></th>
            <th>Cleaned Up</th>
        </tr>
        <tr>
            <td>Phone</td>
            <td>EUNE</td>
            <td>Yes</td>
            <td>04/04/20</td>
        </tr>
    </table> 
    <br>
      
    <?php
        $currPage = 1;
        echo '<div id="pagination-footer">';
        echo '<p>Pages</p>';
        while($currPage <= $pages){
            if($currPage == $pages){
                if($page_number == $currPage){
                    echo '<a href="purchase?page=' . $currPage . "&region=" .$region . '"><b><u>' . $currPage . '</u></b></a>';
                } else {
                    echo '<a href="purchase?page=' . $currPage . "&region=" .$region . '">' . $currPage . '</a>';
                }           
            } else {
                if($page_number == $currPage) {
                    echo '<a href="purchase?page=' . $currPage . "&region=" .$region . '"><b><u>' . $currPage . '</b></u></a> -';
                } else {
                    echo '<a href="purchase?page=' . $currPage . "&region=" .$region . '">' . $currPage . '</a> - ';
                }             
            }
 
            $currPage++;
        }
        echo '</div>';
        ?>
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