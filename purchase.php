<?php
    include 'assets/php/db.php';
    echo '<script src="https://shoppy.gg/api/embed.js"></script>';
    $dbHandler = new databasehandler();
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
                <a id="header-link" href="purchase">Bored of searching?</a> -
                <a id="header-link" href="about">About Us</a> -
                <a id="header-link" href="privacy">Privacy Policy</a>
        </div>
    </div>

    <hr class="break">
    <h2 id="avail-names-header">Available Names</h2>
    <table id="key-table">
        <tr>
            <th>Icon</th>
            <th>Explanation</th>
        </tr>
        <tr>
            <td><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"></td>
            <td>Over 25 Recent Searches</td>
        </tr>
        <tr>
            <td><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"></td>
            <td>Over 75 Recent Searches</td>
        </tr>
        <tr>
            <td><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"></td>
            <td>Over 125 Recent Searches</td>
        </tr>
        <tr>
            <td><img alt="Price increase soon." id="sales-img" src="/assets/arrow.png"></td>
            <td>Over 450 Recent Searches, price increase soon.</td>
        </tr>
    </table> 
    <br>
    <table id="purchase-table">
        <tr>
            <th>Summoner Name</th>
            <th>Region</th>
            <th>Price</th>
            <th>Searches</th>
            <th>Purchase Link</th>
        </tr>
        <tr>
            <?php     
                $page_number = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
                $region = htmlspecialchars(isset($_GET['region']) ? $_GET['region'] : "all");
                $start = ($page_number - 1) * $limit;

                if($region == "all"){
                    $sqlQuery = "SELECT * FROM leagjlhp_owned.lg_names WHERE sold = 0 ORDER BY region LIMIT $start, $limit";
                    $sqlQuery2 = "SELECT COUNT(*) FROM leagjlhp_owned.lg_names WHERE sold = 0";
                } else {
                    $sqlQuery = "SELECT * FROM leagjlhp_owned.lg_names WHERE sold = 0 AND region = \"$region\" LIMIT $start, $limit";
                    $sqlQuery2 = "SELECT COUNT(*) FROM leagjlhp_owned.lg_names WHERE sold = 0 AND region = '$region'";
                }
                
                $nameInfo = $dbHandler->mysqli->query($sqlQuery); 

                
                $totalRows = $dbHandler->mysqli->query($sqlQuery2);   
                $totalRowCount = 0;

                foreach($totalRows as $row) {
                    $totalRowCount = $row['COUNT(*)'];
                }       

                $pages = ceil($totalRowCount / $limit);

                if(!empty($nameInfo)){
                    while($row = $nameInfo->fetch_assoc()){
                        if(intval($row['searches']) >= 25 && intval($row['searches']) < 75){
                            $srcString = '<td>' . $row['searches'] . '<img alt="Hot Name" id="sales-img" src="/assets/fire.gif">' . '</td>';
                        } elseif (intval($row['searches']) >= 75 && intval($row['searches']) < 125) {
                            $srcString = '<td>' . $row['searches'] . '<img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif">' . '</td>';
                        }  elseif (intval($row['searches']) >= 125 && intval($row['searches']) < 450) {
                            $srcString = '<td>' . $row['searches'] . '<img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif">' . '</td>';
                        } elseif (intval($row['searches']) >= 450) {
                            $srcString = '<td>' . $row['searches'] . '<img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Hot Name" id="sales-img" src="/assets/fire.gif"><img alt="Price increase soon." id="sales-img" src="/assets/arrow.png">' . '</td>';
                        } else {
                            $srcString = '<td>' . $row['searches'] . '</td>';
                        }

                        echo '<tr>' . 
                        '<td><b>' . $row['summoner'] . '</b></td>' .
                        '<td>' . $row['region'] . '</td>' .
                        '<td>£' . $row['price'] . '</td>' .
                        $srcString .
                        '<td>' .  '<button id="pur-button" data-shoppy-product="' . $row['link'] . '">Purchase</button>' . '</td>' .
                        '</tr>';
                    }
                } else {
                    echo "no results.";
                }
            ?>
        </tr>
    </table>    
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