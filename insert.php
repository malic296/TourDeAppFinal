<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
        <header><h1>Add Note</h1></header>

        <div class="all">
            <form method="post" class = "insert-form">
                <label for="name">Note Name</label>
                <input type="text" name="nameofnote" id = "name" required class = "input-text-insert" maxlength = "20"></input><br>

                <label for="autor">Note Author</label>
                <input type="text" name="nameofautor" id = "autor" required class = "input-text-insert" maxlength = "30"></input><br>

                <label for="text">Note</label>
                <input type="text" name="notetext" id = "text" required class = "input-text-insert" maxlength = "120"></input><br>

                <label for="date">Deadline</label>
                <input type="date" id="dateend" name="dateend" id = "date" required class = "input-text-insert"><br>


                <label for="priority"></label>
                <select name = "priority" id = "priority" class = "priority">
                    <option value="1">Low Priority</option>
                    <option value="2">Medium Priority</option>
                    <option value="3">High Priority</option>
                </select>

                <input class="insert-creation-btn" type="submit" name="submit" value ="Add">        
            </form>

        </div>
        <div class="menu-insert">
            <a href = "notes.php" class="note-insert-btn">Go Back</a>
        </div>
</body>
</html>

<?php 
include "connection.php";
include "todaydate.php";
?>

<?php 

//zpracování dotazu
        if (isset($_POST["submit"])){
            //form to $
                $note_nazev = $_POST["nameofnote"];
                $autor_name = $_POST["nameofautor"];
                $text_note = $_POST["notetext"];
                $date_start = $today;
                $date_finnish = $_POST["dateend"];
                $priority_note = $_POST["priority"];
            //end form to $
                $dotaz = "INSERT INTO StickyNote VALUES (null, '$note_nazev', '$autor_name','$text_note','$date_start','$date_finnish',$priority_note, 0);";
                $vysledek = mysqli_query($connection, $dotaz);
                

                // Add a custom function to create the alert box with custom styles
                echo "<script type='text/javascript'>
                function alertBox(message) {
                    var overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = 0;
                    overlay.style.left = 0;
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                    overlay.style.zIndex = '9999';

                    var alertBox = document.createElement('div');
                    alertBox.style.position = 'absolute';
                    alertBox.style.top = '50%';
                    alertBox.style.left = '50%';
                    alertBox.style.width = '600px';
                    alertBox.style.height = '350px';
                    alertBox.style.transform = 'translate(-50%, -50%)';
                    alertBox.style.backgroundColor = '#fff';
                    alertBox.style.padding = '20px';
                    alertBox.style.borderRadius = '5px';
                    alertBox.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.5)';
                    alertBox.style.zIndex = '10000';
                    alertBox.classList.add('box-popup-insert');
                    
                    var messageElem = document.createElement('p');
                    messageElem.innerText = message;
                    messageElem.style.margin = 0;
                    messageElem.style.height = '50px';
                    messageElem.classList.add('text-popup-insert');
                    
                    
                    
                    var closeButton = document.createElement('button');
                    closeButton.innerText = 'Close';
                    closeButton.classList.add('btn-popup-insert');
                    closeButton.addEventListener('click', function() {
                        overlay.remove();
                    });
                    
                    alertBox.appendChild(messageElem);
                    alertBox.appendChild(closeButton);
                    overlay.appendChild(alertBox);
                    
                    document.body.appendChild(overlay);
                }
                </script>";

                $message = "Note was created";
                echo "<script type='text/javascript'>alertBox('$message');</script>";

                if (!$vysledek) {
                    printf("Error: %s\n", mysqli_error($connection));
                    exit();
                }
                else{
                    
                }
                
               
        }
        else if(isset($_POST["back"])){
            header("Location: notes.php");
        }
        ?>
        </body>
        