<?php
$CRUD_FILE_PAGE='
  <?php
session_start();
mysql_connect(\''.$_SESSION["dbaddr"].'\',\''.$_SESSION['dbid'].'\',\''.$_SESSION['dbpass'].'\');
$db=mysql_select_db(\''.$_SESSION['dbname'].'\');        


//Filter for SQL Injection
function filter($str)
{
    return addcslashes(mysql_real_escape_string($str),\'%_\');
}
?>
  
';

?>