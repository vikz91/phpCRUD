<?php
session_start();
//error_reporting(0);
$m=' ';

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>test phpCRUD</title>
        <link rel="stylesheet" href="css/general.css" media="screen"/>
    </head>
    <body>
     <form id="frm" name="frm" method="post">
        <h3>Form Controls</h3>
            <label><input type="checkbox" name="chk0" />Check 1</label>
            <label><input type="checkbox" name="chk1" />Check 2</label>
            <label><input type="checkbox" name="chk2" />Check 3</label>
          <input type="submit" name="btn" value="Submit"  /> 
        
        <h3>Testing</h3>
        <h4>All Defined variables</h4>
        <pre>
            <?php print_r(get_defined_vars()); ?>
        </pre>
     </form>
    </body>
</html>