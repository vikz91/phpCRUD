<?php
$CRUD_FILE_PAGE='
<?php
//Includes
include_once(\'_dbcon.php\');

//$_GET Filteration Security
$action=(isset($_GET[\'action\']))?filter($_GET[\'action\']):null;
$record_id=(isset($_GET[\'record_id\']))?filter($_GET[\'record_id\']):null;

$keyVal=$record_id;

$fld_val=array();
//Setting up $fld_val default value
for($i1=0;$i1<'.count($fld_val).';$i1++)
    $fld_val[]=null;
    

$m="Welcome to CRUD"; //Default Message
                            

/*
    ===============
    CRUD OPERATIONS
    ===============
*/

   
if(isset($_POST[\'btnSave\']))
    {
        if($action==\'add\') // Creating a Record
        {
            //Incase Field is set to auto_increment, not alowing Field to be inserted
            
';  
            if($fld_array[0][4]==true && $fld_array[0][4]=='AUTO_INCREMENT') //If it is Primary Key & is A_I
            {
                $qry_s='\'insert into '.$tbl.'('.$fld_array[1][0];
                for($i1=2;$i1<count($fld_val);$i1++)
                     $qry_s.=','.$fld_array[$i1][0];
                 $qry_s.=') values (\'';
            }
            else
            {                
                $qry_s='\'insert into '.$tbl.'('.$fld_array[0][0];
                for($i1=1;$i1<count($fld_val);$i1++)
                    if(!(($fld_array[$i1][0]==$key && $fld_array[$i1][1]==true) &&$key_ai==true))
                        $qry_s.=",".$fld_array[$i1][0];
                 $qry_s.=') values (\\\'\'.$_POST["inp_'.$fld_array[0][0].'"]';
            }
            for($i1=1;$i1<count($fld_val);$i1++)
                if($fld_array[$i1][0]!=$key)
                    $qry_s.='.\'\\\',\\\'\'.$_POST["inp_'.$fld_array[$i1][0].'"]';
            $qry_s.='.\'\\\')\';';
$CRUD_FILE_PAGE.='            
            $qry_s='.$qry_s.'
            if(mysql_query($qry_s))
                $m=$_POST[\'inp_'.$fld_array[0][0].'\']." is added successfully.";
            else
                $m="Record Creation failed! Try a different ID";
                
            
        }
        else //Updating a Record
        {
           //Not Allowing num Key to be updated if it is No
 ';        if($fld_array[0][4]==true && $fld_array[0][1]==true)
            {
                $qry_s='\'update '.$tbl.' set '.$fld_array[1][0].'=\\\'\'.$_POST["inp_'.$fld_array[1][0].'"]';
                $i1=2;
            }                
            else
            {
                $qry_s='\'update '.$tbl.' set '.$fld_array[0][0].'=\\\'\'.$_POST["inp_'.$fld_array[0][0].'"]';
                $i1=1;
            }           
                
            for(;$i1<count($fld_val);$i1++)
                if($fld_array[$i1][0]!=$key)
                    $qry_s.='.\'\\\','.$fld_array[$i1][0].'=\\\'\'.$_POST["inp_'.$fld_array[$i1][0].'"]';;
            $qry_s.='.\'\\\' where '.$key.'=\\\'\'.$keyVal.\'\\\'\';';
 $CRUD_FILE_PAGE.='           
            $qry_s='.$qry_s.'
            if(mysql_query($qry_s))
                $m=$_POST[\'inp_'.$fld_array[0][0].'\']." is updated successfully.";
            else
                $m="Record Updation failed!";
        }
        
        header(\'location:'.$pname.'_view.php?m=\'.$m);
    }


      switch($action)
{
    
   //Fetching data from DB to fields for editing
    case \'edit\':
        if($record_id==null)
            break;
            $qry_s=\'select * from '.$tbl.' where '.$key.'=\\\'\'.$keyVal.\'\\\'\';
            $res_s=mysql_query($qry_s);
            if(mysql_num_rows($res_s)>0){
                for($j1=0;$j1<'.count($fld_val).';$j1++)
                    $fld_val[$j1]=mysql_result($res_s,0,$j1);
                $m=\'Editing Record of \'.mysql_result($res_s,0,0);
            }
            else
                $m=\'Selected Records Dosen\\\'t Exist.\';
                
        break;    
    
    //Update Edited data fields to DB
    case \'update\':
        if($record_id==null)
            break;
    
        break;
    
    // Delete an Existing Record and reutrn to View
    case \'delete\':
        if($record_id==null)
            break;
        $qry_s=\'delete from '.$tbl[$i].' where '.$key.'=\\\'\'.$keyVal.\'\\\'\';
        if(mysql_query($qry_s))
            $m=\'Record Deleted.\';
        else
            $m=\'Deletion Error!\';
        
        header(\'location:'.$pname.'_view.php?m=\'.$m);
        break;
        
    // Add a new record and return to View
    default: //\'create\'
        if($record_id==null)
            break;

        break;
}
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
        </div>    <br />
        <form id="frm" name="frm" method="post">
            <h3>user_table</h3>
            <div id="msg"> <?php echo $m;?> </div>
            <br />
            <table>
 <?php
                    $k=0;';
                    foreach($fld_array as $val)
                    {
                       if(!($val[0]==$key && $val[1]==true))
                       {
                        
                       }
                       
                       $type="text"; 
                        
                        //Checking for Password Field
                        if($val[0]=='password' || $val[0]=='pass' || $val[0]=='upass' || $val[0]=='u_pass')
                           $type="password";
                                                
                        $CRUD_FILE_PAGE.= ' echo \'<tr>
                                 <td>'.ucfirst($val[0]).'</td>
                                <td><input type="'.$type.'" name="inp_'.$val[0].'" value="\'.$fld_val[$k++].\'" /></td>
                        </tr>\';';
                    }
                    unset($val,$k);
               
$CRUD_FILE_PAGE.=' 
                ?> 
                <tr>
                    <td style="text-align:right;" colspan="2">
                        <a href="'.$pname.'_view.php" class="btnDel" title="Return Back">Cancel</a>
                        <input type="submit" name="btnSave" value="Save" />
                    </td>
                </tr> 
            </table>
                
        </form>
    </body>
</html>                 
 ';
 
 ?>