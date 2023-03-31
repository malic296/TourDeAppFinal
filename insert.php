<?php 
include "connection.php";
include "todaydate.php";
?>

<?php 

            ?>
            <body>
            <h1>Test</h1>
            <form method="post">
            <input type="text" name="nameofnote" placeholder="Name Of Note"></input><br>
            <input type="text" name="nameofautor" placeholder="Your Name"></input><br>
            <input type="text" name="notetext" placeholder="Text Of Your Note"></input><br>
            <input type="date" id="dateend" name="dateend" value="<?php echo date('d.m.Y'); ?>"><br>
            <input type="radio" name="priority" value="1" checked="">Low Priority<br>
            <input type="radio" name="priority" value="2">Higher Priority<br>
            <input type="radio" name="priority" value="3">High Priority<br>
            <input type="submit" name="submit" value ="Insert"><br>
            <br>
            <?php
//zpracování dotazu
        if (isset($_POST["submit"])){
            //form to $
                $note_nazev = $_POST["nameofnote"];
                $autor_name = $_POST["nameofautor"];
                $text_note = $_POST["notetext"];
                $finnishdate = $_POST["todaydate"];
                $date_start = $today;
                $date_finnish = $_POST["dateend"];
                $priority_note = $_POST["priority"];
            //end form to $
                $dotaz = "INSERT INTO StickyNote VALUES (null, '$note_nazev', '$autor_name','$text_note','$date_start','$date_finnish',$priority_note, 0);";
                $vysledek = mysqli_query($connection, $dotaz);
                if (!$vysledek) {
                    printf("Error: %s\n", mysqli_error($connection));
                    exit();
                }
                
               
        }else{echo "lol";}
        ?>
        </form>
            </body>
        