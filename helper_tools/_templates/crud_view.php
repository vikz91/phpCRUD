<?php
$CRUD_FILE_PAGE='
<?php
//Includes
include_once(\'_dbcon.php\');

//$_GET Filteration Security
$action=(isset($_GET[\'action\']))?filter($_GET[\'action\']):null;
$record_id=(isset($_GET[\'record_id\']))?filter($_GET[\'record_id\']):null;

$keyVal=$record_id;

$m="Welcome to CRUD"; //Default Message
?>
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
        </div>     <br />
        <form id="frm" name="frm" method="post">
            <h3>user_table</h3>
            <div id="msg"> <?php echo (isset($_GET[\'m\']))?$_GET[\'m\']:\' \';?> </div>
            <br />
            <a href="'.$pname.'.php?action=add"><span style="font-size: 1.4em;">+</span> Add New</a>
            <table id="dataTable">
                <tr> 
                    ';
                    foreach($fld_array as $v)
                        //if(!($v[0]=='pass' || $v[0]=='password'))
                            $CRUD_FILE_PAGE.='<th>'.$v[0].'</th>';
                    
                 $CRUD_FILE_PAGE.='   <th>[ Actions ]</th>
                </tr>
                                    
  ';
                    $rec_idf;// Record Identifier for int type
                    //Explicitly Checking Key Value Type (true-no, false- not no.)
                    foreach($fld_array as $v)
                        if($v[0]==$key)
                            $rec_idf=$v[0];
                    $CRUD_FILE_PAGE.='
                    <?php
                    $pn=\''.$pname.'\';
                    $rec_idf=\''.$rec_idf.'\';
                    $qry4="select * from '.$tbl.'";
                    $res4=mysql_query($qry4);
                    for($i2=0;$i2<mysql_num_rows($res4);$i2++){       
                        echo "<tr>";
                            for($j2=0;$j2<'.count($fld_array).';$j2++){
                                echo "<td>".mysql_result($res4,$i2,$j2)."</td>";
                            }
                        echo \'
                            <td>
                                <a href="\'.$pn.\'.php?action=edit&record_id=\'.mysql_result($res4,$i2,$rec_idf).\'" class="btnEdit" title="Edit this Record">Edit</a>&nbsp;&nbsp;&nbsp;
                                <a href="\'.$pn.\'.php?action=delete&record_id=\'.mysql_result($res4,$i2,$rec_idf).\'" class="btnDel" title="Delete this Record">Del</a>
                            </td>
                            
                        </tr>\';
                    }
  
                    unset($qry4,$res4,$rec_idf,$v,$i2,$j2);
                    ?>
       
             </table>
                
        </form>
    </body>
</html>  
';
?>