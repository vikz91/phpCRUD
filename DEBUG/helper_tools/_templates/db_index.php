<?php
$CRUD_FILE_PAGE='
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <title>phpCRUD</title>
        <link rel="stylesheet" href="style.css" media="screen"/>
    </head>
    <body>
        <div id="logo">
            <a href="'.$_SESSION["dbname"].'_index.htm" title="back to Home">'.ucfirst($_SESSION["dbname"]).'</a>
        </div>    <br />
        <ul>
        
        '; for($i9=0;$i9<count($tab_f);$i9++)
                $CRUD_FILE_PAGE.= '<li>
                    <a href="'.$_SESSION['dbname'].'_'.strtolower($tab_f[$i9]).'_view.php" title=" ">'.ucfirst($tab_f[$i9]).'</a>
                </li>';
           
            
        $CRUD_FILE_PAGE.='   
        </ul>     
        </form>
    </body>
</html>  
';   

?>   