<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href = "styles.css">
    <title>Notes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on("click", ".delete-note-btn", function() {
        var noteId = $(this).data("note-id");
        $.ajax({
            url: "delete_note.php",
            type: "POST",
            data: {id: noteId},
            success: function() {
                $("#note-" + noteId).remove();
                $(document).ajaxStop(function(){
                window.location.reload();
                });
            }
        });
    });
    </script>

</head>
<body>
    <header>
        <h1>Notes</h1>
    </header>

    <div class="menu">
        <div class="menu1">
            <form action = "insert.php" method = "post">
                <input type="submit" class = "add-btn" value = "+" name="insert">
            </form>
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
        $sql = "SELECT * FROM StickyNote ORDER BY priority_note DESC";
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
                $id = $row["ID"];
                $priority = $row['priority_note'];
                if($priority == 1){
                    $nadpis = "yellow-nadpis";
                    $body = "yellow-body";
                }
                else if($priority == 2){
                    $nadpis = "orange-nadpis";
                    $body = "orange-body";
                }
                else{
                    $nadpis = "red-nadpis";
                    $body = "red-body";
                }



                echo "
                <div class='note'>
                    <div class='note-nadpis $nadpis'>$nazev<br><span class = 'date'>Deadline: $deadline</span></div>
                    <div class='note-text $body'>$text</div>
                    <div class = 'note-podpis $body'>
                        <button class='delete-note-btn'>✎</button>
                        <button class='delete-note-btn' data-note-id='$id'>✘</button>
                        $autor
                    </div>
                </div>
                ";
                $cislo ++;
                if($pomocne_cislo + 3 == $cislo){
                    $pomocne_cislo = $cislo;
                    echo "</div>";
                }
                        
                
            }
            
        }
     
        ?>        

    </div>

    



</body>
</html>