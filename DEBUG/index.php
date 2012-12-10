<?php
/*
[ DEV-codes ] 
NDOPT - Need Optimization
NDSEC - Need Security
NREQ - Not Require;

[ SESSION  VARIABLES ]
$_SESSION['dbaddr']
$_SESSION['dbname']
$_SESSION['dbid']
$_SESSION['dbpass']
$_SESSION['table'][] // To Store array of all tables in a coonected DB 

*/
session_start();
//error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time',300); //1 hour is more than enough...

//Init
$btnGen_enb='value="Connect to a Database First" disabled="disabled"';
$dir_crud='crud_files/';
$dir_tem='_templates/';

//PAGE MODEL - Opening a template and writing it to a file
function crud($TEMPLATE_FILE,$WRITE_AS_FILE)
{
    include($TEMPLATE_FILE);
   
   //Writing file
    $CRUD_FILE_RESOURCE=fopen($WRITE_AS_FILE,'w') or die('can not open the file');
    fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
    fclose($CRUD_FILE_RESOURCE);
    unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);          
}

//DBCON MODEL
function con($SERVER_ADDRESS,$DATABASE_NAME,$SERVER_ID,$SERVER_PASSWORD)
{
     if(mysql_connect($SERVER_ADDRESS,$SERVER_ID,$SERVER_PASSWORD))
        if(mysql_select_db($DATABASE_NAME)) return true; else return false;
     else
        return false;
}

//Identifiers
$IDF=array(
    'pass'=>array('password','pass','upass','usrpass','u_pass','usr_pass'),
    'primary'=>'PRI',
    'null'=>'NULL',
    'no'=>'NO'
);

$err=' ';
$tab_f=array();
        

//Database Connection
if(isset($_POST['btnCon']))
{
    //Restting this variable for undesired results
    unset($_SESSION['table']);
    
    //Storing Form Values in Session for later use. [NDSEC] 
    $_SESSION['dbaddr']=$_POST['dbaddr'];
    $_SESSION['dbname']=$_POST['dbname'];
    $_SESSION['dbid']=$_POST['dbid'];
    $_SESSION['dbpass']=$_POST['dbpass'];
    
    //Connect to the given database
    if(con($_SESSION['dbaddr'],$_SESSION['dbname'],$_SESSION['dbid'],$_SESSION['dbpass']))
    {         
        $qry='show tables from '.$_SESSION['dbname']; // get list of Tables from the connected database
        $res=mysql_query($qry);
        for($i=0;$i<mysql_num_rows($res);$i++) // format each table and store in  $_SESSION['table'][]
            $_SESSION['table'][]=mysql_result($res,$i,0);
            
        if(count($_SESSION['table'])>0)
            $gen_enb='value="Generate Tables"';
    }
    else
    {
       //Unset All DB Session Variables for computational speedup
       unset($_SESSION['dbaddr'],$_SESSION['dbname'],$_SESSION['dbid'],$_SESSION['dbpass']);
       $err='Database Connection Not Successful';
    }                
}


//Generate Forms
if(isset($_POST['btnGen']))
{     
    con($_SESSION['dbaddr'],$_SESSION['dbname'],$_SESSION['dbid'],$_SESSION['dbpass']); //Database Re-Connect
    
    if(isset($_SESSION['table']))
        $tbl=$_SESSION['table'];
    else
        header('location:index.php?ref=logo&m=No Table Available');
    
    for($i=0;$i<count($_SESSION['table']);$i++) // Check selected tables to be generated 
    {
        if(isset($_POST['chk'.$i]))
        {   
              //Check if CRUD is to be Generated
             if(isset($_POST['chkCrud']))
             {                 
                //Getting All Information (Primary Key, etc.)
                $pname=$_SESSION['dbname'].'_'.$tbl[$i]; // [NREQ]
                $qry="desc ".$tbl[$i];
                $res=mysql_query($qry);
                $fld_array=array(); $fld_val=array();
                $tab_f[]=$tbl[$i];
                
                for($i1=0;$i1<mysql_num_rows($res);$i1++){
                    //Setting up Template
                    
                    //Checking for int Value type
                    $str=stripos(mysql_result($res,$i1,1),'int');
                    if($str!==false &&$str==0)
                        $fld_is_num=true;
                    else
                        $fld_is_num=false;
                    
                    //Getting Correct Primary Key
                    if(mysql_result($res,$i1,3)=='PRI')
                    {
                        $key=mysql_result($res,$i1,0);
                       //Getting the Corect Primary Key Value Type
                        $fld_is_key=true;
                    }
                    else
                        $fld_is_key=false;                                                            
                    //Getting is NULL value (boolean)
                    $str=stripos(mysql_result($res,$i1,2),'NO');
                    if($str!==false &&$str==0)
                        $fld_is_null=false;
                    else
                        $fld_is_null=true;
                        
                    //Getting Default Value  
                    $fld_default=mysql_result($res,$i1,4);
                    
                    
                    //Checking Key for Auto_increment                            
                    $fld_extra=mysql_result($res,$i1,5);
                    
                    //Getting all the Fields - Ar[]=array(Field Name, Is No., Is NUll, Its Default Value,is Key, Extra)
                    $fld_array[]=array(mysql_result($res,$i1,0),$fld_is_num,$fld_is_null,$fld_default,$fld_is_key,$fld_extra);
                    /*
                        e.g.
                            $fld_array= array(
                                    0=>'name',false.false,'',false,'',
                                    1=>'id',true,false,'',true,'AUTO_INCREMENT';                                    
                            );                        
                    */                                                            
                    $fld_val[]=null;                            
                                                
               }               
                unset($fld_is_num,$fld_is_null,$fld_default,$fld_extra);
                
                                                
                // GENERATE CRUD PAGE  
               
$CRUD_FILE_PAGE='
<?php
//Includes
include_once(\'_dbcon.php\');

//$_GET Filteration Security
$action=(isset($_GET[\'action\']))?filter($_GET[\'action\']):null;
$record_id=(isset($_GET[\'record_id\']))?filter($_GET[\'record_id\']):null;

$keyVal= is_numeric($record_id)?$record_id:"\'$record_id\'";

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
            if($fld_array[0][4]==true && $fld_array[0][1]==true) //If it is Primary Key & is A_Iis NUM
            {
                $qry_s='\'insert into '.$tbl[$i].'('.$fld_array[1][0];
                for($i1=2;$i1<count($fld_val);$i1++)
                     $qry_s.=','.$fld_array[$i1][0];
                 $qry_s.=') values (\\\'\'.$_POST["inp_'.$fld_array[1][0].'"]';
                 $i1=2;
            }
            else
            {                
                $qry_s='\'insert into '.$tbl[$i].'('.$fld_array[0][0];
                for($i1=1;$i1<count($fld_val);$i1++)
                    if(!(($fld_array[$i1][0]==$key && $fld_array[$i1][1]==true) &&$key_ai==true))
                        $qry_s.=",".$fld_array[$i1][0];
                 $qry_s.=') values (\\\'\'.$_POST["inp_'.$fld_array[0][0].'"]';
                 $i1=1;
            }
            for(;$i1<count($fld_val);$i1++)
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
                $qry_s='\'update '.$tbl[$i].' set '.$fld_array[1][0].'=\\\'\'.$_POST["inp_'.$fld_array[1][0].'"]';
                $i1=2;
            }                
            else
            {
                $qry_s='\'update '.$tbl[$i].' set '.$fld_array[0][0].'=\\\'\'.$_POST["inp_'.$fld_array[0][0].'"]';
                $i1=1;
            }           
                
            for(;$i1<count($fld_val);$i1++)
                if($fld_array[$i1][0]!=$key)
                    $qry_s.='.\'\\\','.$fld_array[$i1][0].'=\\\'\'.$_POST["inp_'.$fld_array[$i1][0].'"]';;
            $qry_s.='.\'\\\' where '.$key.'=\'.$keyVal;';
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
            $qry_s=\'select * from '.$tbl[$i].' where '.$key.'=\'.$keyVal;
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
        $qry_s=\'delete from '.$tbl[$i].' where '.$key.'=\'.$keyVal;
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
                $CRUD_FILE_RESOURCE=fopen($dir_crud.$_SESSION['dbname'].'_'.$tbl[$i].'.php','w') or die('can not open the file');
                fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
                fclose($CRUD_FILE_RESOURCE);
                unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);      




                // GENERATE VIEW PAGE        
                
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
            <table id="dataTable" cellspacing="0">
                <?php 
                $table_row=0;
                ?>
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
                    $qry4="select * from '.$tbl[$i].'";
                    $res4=mysql_query($qry4);
                    for($i2=0;$i2<mysql_num_rows($res4);$i2++){  
                        
                        echo "<tr \'($table_row++%2)=0?\'class=\"even\"\':\" \">";
                            for($j2=0;$j2<'.count($fld_array).';$j2++){
                                  $msr=mysql_result($res4,$i2,$j2);
                                if(strlen($msr)>20)
                                    $msr=\'<span title="\'.$msr.\'">\'.substr($msr,0,14).\'...\'.\'</span>\';
                                
                               echo "<td>".$msr."</td>";
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
                $CRUD_FILE_RESOURCE=fopen($dir_crud.$_SESSION['dbname'].'_'.$tbl[$i].'_view.php','w') or die('can not open the file');
                fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
                fclose($CRUD_FILE_RESOURCE);
                unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);      



            
           } //End of Check CRUD
           else
           {
             //If CRUD is not Used, Generate Simple Web Forms
           }
                            
        } //end of checking $_POST['chk'.$i]
        
    }// END OF Iterating for checking which tables to be generated
        
    //If CRUD is selected, generate library  files
    if(isset($_POST['chkCrud']))
    {                
         //GENERATE DB INDEX PAGE
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
        
        '; foreach($tab_f as $tbf)
                $CRUD_FILE_PAGE.= '<li>
                    <a href="'.$_SESSION['dbname'].'_'.strtolower($tbf).'_view.php" title=" ">'.ucfirst($tbf).'</a>
                </li>';
           
            
        $CRUD_FILE_PAGE.='   
        </ul>     
        </form>
    </body>
</html>  
'; 
           $CRUD_FILE_RESOURCE=fopen($dir_crud.$_SESSION['dbname'].'_index.htm','w') or die('can not open the file');
            fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
            fclose($CRUD_FILE_RESOURCE);
            unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);
            
            
            
         //GENERATE CSS FILE  
         
$CRUD_FILE_PAGE='
*{padding:0;margin:0}
body
{
	font-family:arial;
	font-size:0.85em;
    color:#4F4F4F;
    margin:0 auto;
    margin-left:30px;
    margin-top:10px;
}
a{
    color:#07A5F9;
    text-decoration: none;
}
a:hover{
    text-decoration: underline;
    text-shadow: 0 0 5px #ABABAB;
}
#logo a{
    text-decoration: none;
}
#logo{
  font-size: 3em;
  font-weight: bold;
  color:#FE9101;  
  padding:5px 10px;
  margin:3px;
  transition: all 800ms ease-in-out;
}
#logo:hover{
    color:#ABABAB;
    box-shadow:0 0 10px #BCBCBC;
    border-radius:40px;
}
#msg{
    box-shadow:0 0 10px #9E9E9E;
    padding:5px 10px;
    margin:5px;
    width:auto;
    border-radius:4px;
    background-color: #FFEFAE;
}
.btnDel{
    color:red;

}
.btnEdit{
    color:green;
}
table a:link {
	color: #666;
	font-weight: bold;
	text-decoration:none;
}
table a:visited {
	color: #999999;
	font-weight:bold;
	text-decoration:none;
}
table a:active,
table a:hover {
	color: #bd5a35;
	text-decoration:underline;
}
table {
	font-family:Arial, Helvetica, sans-serif;
	color:#666;
	font-size:12px;
	text-shadow: 1px 1px 0px #fff;
	background:#eaebec;
	margin:20px;
	border:#ccc 1px solid;

	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;

	-moz-box-shadow: 0 1px 2px #d1d1d1;
	-webkit-box-shadow: 0 1px 2px #d1d1d1;
	box-shadow: 0 1px 2px #d1d1d1;
}
table th {
	padding:21px 25px 22px 25px;
	border-top:1px solid #fafafa;
	border-bottom:1px solid #e0e0e0;

	background: #ededed;
	background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
	background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
table th:first-child{
	text-align: left;
	padding-left:20px;
}
table tr:first-child th:first-child{
	-moz-border-radius-topleft:3px;
	-webkit-border-top-left-radius:3px;
	border-top-left-radius:3px;
}
table tr:first-child th:last-child{
	-moz-border-radius-topright:3px;
	-webkit-border-top-right-radius:3px;
	border-top-right-radius:3px;
}
table tr{
	text-align: center;
	padding-left:20px;
}
table tr td:first-child{
	text-align: left;
	padding-left:20px;
	border-left: 0;
}
table tr td {
	padding:18px;
	border-top: 1px solid #ffffff;
	border-bottom:1px solid #e0e0e0;
	border-left: 1px solid #e0e0e0;
	
	background: #fafafa;
	background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
	background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}
table tr.even td{
	background: #f6f6f6;
	background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
	background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
table tr:last-child td{
	border-bottom:0;
}
table tr:last-child td:first-child{
	-moz-border-radius-bottomleft:3px;
	-webkit-border-bottom-left-radius:3px;
	border-bottom-left-radius:3px;
}
table tr:last-child td:last-child{
	-moz-border-radius-bottomright:3px;
	-webkit-border-bottom-right-radius:3px;
	border-bottom-right-radius:3px;
}
table tr:hover td{
	background: #f2f2f2;
	background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
	background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);	
}
';
         
           $CRUD_FILE_RESOURCE=fopen($dir_crud.'style.css','w') or die('can not open the file');
            fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
            fclose($CRUD_FILE_RESOURCE);
            unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);
            
            
        //GENERATE _dbcon FILE
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
        
           $CRUD_FILE_RESOURCE=fopen($dir_crud.'_dbcon.php','w') or die('can not open the file');
            fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
            fclose($CRUD_FILE_RESOURCE);
            unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);
            
      
        header('location:'.$dir_crud.$_SESSION['dbname'].'_index.htm'); //Open the generated DB Index Page
    }          
    
    //Releasing Used resources for computational speedup and avoiding any conflict
     unset($_SESSION);
     unset($IDF);
} // End of btnGen button's POST action 
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>phpCRUD</title>
        <link rel="stylesheet" href="css/general.css" media="screen"/>
        
       
    </head>
    <body onload="chkChk()">
     <form id="frm" name="frm" method="post">
        <div id="header">
             <div id="logo"><a href="index.php?ref=logo" ><span id="logo1">php</span><span id="logo2">CRUD</span></a></div>
           <div id="banner">
                <h1><i>php</i><b>Crud</b> - Generate Web Forms, tables from mySQL database in fastest and easiest way.</h1>
            </div>
            <div class="clr"></div>
        </div>
        <div id="content">
            <div id="msg">
                <?php echo $err; ?>
                <?php echo isset($_GET['m'])?'<br />'.$_GET['m']:' '; ?>
            </div>
           <div id="databaseConnect">
                <h3>Database Details</h3>
                <table>
                    <tr>
                        <td>Server Address</td>
                        <td><input type="text" name="dbaddr" value="localhost"/></td>
                    </tr>
                    <tr>
                        <td>Database Name</td>
                        <td><input type="text" name="dbname" value="test" /></td>
                    </tr>
                    <tr>
                        <td>Login ID</td>
                        <td><input type="text" name="dbid" value="root" /></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><input type="password" name="dbpass" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <input type="reset" name="btnRes" value="Clear" />
                            <input type="submit" name="btnCon" value="Connect" />
                        </td>
                    </tr>
                </table>
            </div> <br />
            <div id="selectTable">
                
                <h3>    Tables in <b><i> '<?php echo isset($_SESSION['dbname'])?$_SESSION['dbname']:' '; ?>' </i></b></h3>
                <div id="tblContainer">
                    <table>
                        <?php 
                            if(!isset($_SESSION['table'])){
                                echo 'NO Available tables';
                                
                            }
                            else
                              {  for($i=0;$i<count($_SESSION['table']);$i++) //List All Tables for selection
                                    echo "\n".'<tr><td><label><input type="checkbox" name="chk'.$i.'" />&nbsp;
                                    '.$_SESSION['table'][$i].' </label></td></tr>';
                                $btnGen_enb="Generate Forms";
                              }
                         ?>                       
                    </table>
                </div>
                <label ><input type="checkbox" id="chkAll" name="chkAll" onclick="checkAll(this)"  /> All Tables</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label><input type="checkbox" id="chkCrud" name="chkCrud" /> CRUD</label>&nbsp;&nbsp;
                <input type="submit" name="btnGen" <?php echo $btnGen_enb; ?> />
            </div>
           </div>
        <div id="footer">
            &copy; 2012, <a href="mailto:vikz91.deb@gmail.com">theCodeCult</a>. All Rights Reserved.
        </div>
      </form>
      
      
      
       <script type="text/javascript">
            function checkAll(bx) {
                var cbs = document.getElementsByTagName('input');
                for(var i=0; i < cbs.length; i++) {
                    if(cbs[i].type == 'checkbox') {
                      cbs[i].checked = bx.checked;
                    }
                }
            }
            
            function chkChk(){
                var c=document.getElementById('chkAll');
                c.checked=true;
                checkAll(chkAll);
            }
        </script>
      
        <div id="test">
            <pre>
                <?php print_r(get_defined_vars()); ?>
            </pre>
        </div>
      
    </body>
</html>