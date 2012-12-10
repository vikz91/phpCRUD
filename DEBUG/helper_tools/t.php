<?php
session_start();
//error_reporting(0);

if(isset($_POST['btnUnsetSes']))
    unset($_SESSION);

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>test phpCRUD</title>
        <link rel="stylesheet" href="css/general.css" media="screen"/>
    </head>
    <body>

     <form id="frm" name="frm" method="post">
        <input type="submit" name="btnUnsetSes" value="Unset Sessions" />
        <input type="submit" name="btn" value="Send" disabled="enabled" />
        <table>
            <tr>
                <td>Cell 1</td>
                <td><span title="This is a long cell">Cell 2</span></td>
            </tr>
        </table>
        <?php
           
           $s="Hello"; 
            echo "Chars: ".strlen($s);
        ?>
        
        <h3 style="margin-top:50px;">Testing</h3>
        <h4>All Defined variables</h4>
        <pre>
            <?php print_r(get_defined_vars()); ?>
        </pre>
     </form>
    </body>
</html>