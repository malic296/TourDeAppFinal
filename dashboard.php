<?php

include("connection.php");

$url = 'https://tda.knapa.cz/';
$token = '3bfb87fefd3215609504d97ecbd8b5a6';

//pošle api, parametry - $url je vždy $url, $sub je jedno ze tří[$sysinfo, $commit, $user] pak $token je taky vždy token,
//$what dejte textový řetězec bud "data"(return bude array) nebo "response"(vyhodí json všech hodnot)
function sendApi($url, $sub, $token, $what){
    $api_url = $url . $sub;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'x-access-token: '.$token
    ));
    $response = curl_exec($ch);

    if ($response === false) {
        die(curl_error($ch));
    }
    $data = json_decode($response, true);
    if($what == "response"){
        return $response;
    }else if($what == "data"){
        return $data;
    }
}

$latestAPI = "commit/latest/1";
$latestOutput = sendApi($url, $latestAPI, $token, "data");


$latestOutput = $latestOutput[0];

$creator = $latestOutput['creator_id'];
$date =  $latestOutput['date'];
$linesAdd = $latestOutput['lines_added'];
$linesRem = $latestOutput['lines_removed'];
$description =  $latestOutput['description'];
$id =  $latestOutput['commit_id'];

$latestSelect = "SELECT latestID from APIlatestCommit";
$resultSelect = mysqli_query($connection, $latestSelect);
$dbID = mysqli_fetch_array($resultSelect);

//echo "$creator $date $linesAdd $linesRem $description $id";

if($dbID != $id){
    $deleteLatest ="DELETE FROM APIlatestCommit";
    mysqli_query($connection, $deleteLatest);
    $latestSQL = "INSERT INTO APIlatestCommit (latestCreatorID, latestDate, latestAdd, latestRem, latestDesc, latestID) VALUES ('$creator', '$date', $linesAdd, $linesRem, '$description', '$id')";
    mysqli_query($connection, $latestSQL);

    $sysAPI = "sysinfo/";
    $sysOutput = sendApi($url, $sysAPI, $token, "data");
    $bootTime = $sysOutput['boot_time'];

    $deleteSys = "DELETE FROM APIsysinfo";
    $sysSQL = "INSERT INTO APIsysinfo(sysBootTime) values ('$bootTime')";
    mysqli_query($connection, $sysSQL);

    $commitAPI = "commit/";
    $commitOutput = sendApi($url, $commitAPI, $token, "data");
    $pocetCommit = count($commitOutput);

    $values = "";
    for($i = 0; $i < $pocetCommit; $i++){
        $aktualni = $commitOutput[$i];

        $commitCreator = $aktualni['creator_id'];
        $commitDate =  $aktualni['date'];
        $commitLinesAdd = $aktualni['lines_added'];
        $commitLinesRem = $aktualni['lines_removed'];
        $commitDescription =  $aktualni['description'];
        $commitId = $aktualni['commit_id'];
        
        if($i == $pocetCommit - 1){
            $values .= "('$commitCreator', '$commitDate', $commitLinesAdd, $commitLinesRem, '$commitDescription', '$commitId')";
        }else{
            $values .= "('$commitCreator', '$commitDate', $commitLinesAdd, $commitLinesRem, '$commitDescription', '$commitId'), ";
        } 
    }

    $deleteCommit = "DELETE FROM APIcommit";
    mysqli_query($connection, $deleteCommit);
    $commitSQL = "INSERT INTO APIcommit () VALUES " . $values;
    mysqli_query($connection, $commitSQL);

    $userAPI = "user/";
    $userOutput = sendApi($url, $userAPI, $token, "data");
    $pocetUser = count($userOutput);
    $values = "";
    for($i = 0; $i < $pocetUser; $i++){
        $aktualni = $userOutput[$i];

        $userName = $aktualni['name'];
        $userSurname =  $aktualni['surname'];
        $userNick = $aktualni['nick'];
        $userID = $aktualni['userID'];
        
        if($i == $pocetUser - 1){
            $values .= "('$userName', '$userSurname', '$userNick', '$userID')";
        }else{
            $values .= "('$userName', '$userSurname', '$userNick', '$userID'), ";
        } 
    }

    $deleteUser ="DELETE FROM APIuser";
    $userSQL = "INSERT INTO APIuser (userName, userSurname, userNick, userID) VALUES " . $values;
    mysqli_query($connection, $userSQL);
}

$laCommit = "SELECT APIuser.userName, APIuser.userSurname ,APIuser.userNick, latestDate, latestAdd, latestRem, latestDesc, latestID FROM APIlatestCommit JOIN APIuser ON APIlatestCommit.latestCreatorID = APIuser.userID";
$query = mysqli_query($connection, $laCommit);
$row = mysqli_fetch_array($query);

//dataOutput
$laUserName = $row[0];
$laUserSurname = $row[1];
$laUserNick = $row[2];
$laUserDate = $row[3];
$laFixDate = explode("T", $laUserDate);

$laUserAdd = $row[4];
$laUserRem = $row[5];
$laUserDesc= $row[6];
$laID = $row[7];
//

$groupCommit = "SELECT APIuser.userNick, COUNT(commitCreatorID) FROM APIcommit JOIN APIuser ON APIcommit.commitCreatorID = APIuser.userID GROUP BY commitCreatorID;";
$query = mysqli_query($connection, $groupCommit);

$userNameList = [];
$userCountList = [];
$index = 0;
while($row = mysqli_fetch_array($query)){
    $userNameList[$index] = $row[0];
    $userCountList[$index] = $row[1];
    $index++;
}

$mostSQL = "SELECT APIuser.userName, APIuser.userSurname,APIuser.userNick, COUNT(commitCreatorID) as pocet FROM APIcommit JOIN APIuser ON APIcommit.commitCreatorID = APIuser.userID GROUP BY commitCreatorID ORDER BY pocet desc limit 1;";
$query = mysqli_query($connection, $mostSQL);
$vysledek = mysqli_fetch_array($query);
$mostName = $vysledek[0];
$mostSurname = $vysledek[1];
$mostNick = $vysledek[2];
$mostPocet = $vysledek[3];

$celkemCommitSQL = "SELECT * from APIcommit";
$query = mysqli_query($connection, $celkemCommitSQL);
$pocetCommit = mysqli_num_rows($query);

$timeSQL = "SELECT * FROM APIsysinfo";
$query = mysqli_query($connection, $timeSQL);
$row = mysqli_fetch_array($query);
$timeDB = $row[0];

$currentTime = date('Y-m-d h:i:s', time());

$fixTimeDB = explode("T", $timeDB);
$timeDB = $fixTimeDB[0] . $fixTimeDB[1];

$date1 = new DateTime($timeDB);
$date2 = new DateTime($currentTime);

$diff = $date1->diff($date2);

$hours = $diff->days * 24 + $diff->h;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href = "styles.css">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Notes</h1>
    </header>

        <div class="menu2">
             <form action = "notes.php" method = "post">
                <input type="submit" name = "notes" value="Notes" class="redir-btn">
             </form>
        </div>
    </div>

    <div class="content">
        <?php
        
        
        
        ?>
        <div class='note-radek'>
            <div class="latestCommit">
                <div class='note-nadpis'>Server běží<br></div>
                <div class='note-text'>
                <?php 
                echo "$hours hodin<br>"
                ?>
                </div>
            </div>
        </div>


        <div class='note-radek'>
            <div class="latestCommit">
                <div class='note-nadpis'>Nejnovější commit<br><span class = 'date'>Commitnuto <?php echo "$laFixDate[0] v $laFixDate[1]" ?></span></div>
                <div class='note-text'>
                <?php 
                echo "Autor: $laUserName \"$laUserNick\" $laUserSurname<br>
                Popis: $laUserDesc<br>
                Přidáno: $laUserAdd řádků<br>
                Odebráno: $laUserRem řádků"
                ?>
                </div>
            </div>
        </div>

        <div class='note-radek'>
            <div class="latestCommit">
                <div class='note-nadpis'>Programátor s nejvíce commity<br></div>
                <div class='note-text'>
                <?php 
                echo "$mostName \"$mostNick\" $mostSurname<br>
                Počet commitů: $mostPocet"
                ?>
                </div>
            </div>
        </div>

        <div class='note-radek'>
            <div class="latestCommit">
                <div class='note-nadpis'>Celkový počet commitů<br></div>
                <div class='note-text'>
                <?php 
                echo "$pocetCommit"
                ?>
                </div>
            </div>
        </div>

        <div class="blanks">
        <div class="blank1" id="radar-chart" style="width:70%;">
            <canvas id="radarChart"></canvas>
        </div> <!-- radar chart -->
    </div>

    </div>

    

</body>
</html>


<script>
    const radarChartDest = document.getElementById('radarChart');
    const radarChartData = {
    labels: <?php echo json_encode($userNameList); ?>,
    datasets: [{
        label:"Users",
        data: <?php echo json_encode($userCountList); ?>,
        fill: true,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgb(255, 99, 132)',
        pointBackgroundColor: 'rgb(255, 99, 132)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgb(255, 99, 132)'
    }]
    };
    new Chart(radarChartDest, {
    type: 'radar',
    data: radarChartData,
    options: {
        elements: {
        line: {
            borderWidth: 3
        },
        }
    },
    });
    </script>
    