<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href = "styles.css">
    <title>Notes</title>
</head>
<body>
    <header>
        <h1>Notes</h1>
    </header>

    <div class="menu">
        <div class="menu1">
            <input type="button" class = "add-btn" value = "+">
        </div>

        <div class="menu2">
             <form action = "dashboard.php" method = "post">
                <input type="submit" name = "dashboard" value="Dashboard" class="redir-btn">
             </form>
        </div>
    </div>

    <div class="notes">

        
        <?php
        //DB note creation
        include "connection.php";
        $sql = "SELECT * FROM StickyNote";
        $sql2 = "SELECT COUNT(ID) as 'pocet' from StickyNote";

        $result2 = $connection->query($sql2);
        if($result2->num_rows>0){
            while($row = $result2->fetch_assoc()){
                $pocet_zaznamu = $row['pocet'];
            }
        }

        $cislo = 1;
        $pomocne_cislo = 1;
        $result = $connection->query($sql);
        if($result->num_rows > 0){     
            while($row = $result->fetch_assoc()){
                if($pomocne_cislo == $cislo){
                    echo "<div class='note-radek'>";
                }  
                $nazev = $row['note_nazev'];
                $text = $row['text_note'];
                $autor = $row['autor_nazev'];
                $deadline = $row['date_finnish'];
                echo "
                <div class='note'>
                    <div class='note-nadpis'>$nazev<br><span class = 'date'>Deadline: $deadline</span></div>
                    <div class='note-text'>$text</div>
                    <div class = 'note-podpis'>$autor</div>
                </div>
                ";
                if($pomocne_cislo == $cislo){
                    echo "</div>";
                    
                }

                if($pomocne_cislo == $cislo){
                    $pomocne_cislo = $pomocne_cislo + 3;
                }

                $cislo++;
                
                
            }
        }



        
        ?>

        <div class='note-radek'>
            <div class='note'>
                <div class='note-nadpis'>Úprava webu<br><span class = 'date'>Deadline: 21.7.2023</span></div>
                <div class='note-text'></div>
            </div>
            
        </div>
        

    </div>



</body>
</html>