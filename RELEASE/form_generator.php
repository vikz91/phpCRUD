<?php
session_start();
error_reporting(0);

$err=' '; $tbl=' '; $msg=' '; $flag=false;
$dbaddr= ' ';
$dbname= ' ';
$dbid= ' ';
$dbpass= ' ';
$table=array();


//DBCON MODEL
function con($SERVER_ADDRESS,$DATABASE_NAME,$SERVER_ID,$SERVER_PASSWORD)
{
     if(mysql_connect($SERVER_ADDRESS,$SERVER_ID,$SERVER_PASSWORD))
        if(mysql_select_db($DATABASE_NAME))
           return true;
        else
            return false;
     else
        return false;
}


//Database Connection
if(isset($_POST['btnCon']))
{
    $dbaddr=$_POST['dbaddr'];
    $dbname=$_POST['dbname'];
    $dbid=$_POST['dbid'];
    $dbpass=$_POST['dbpass'];
    
    if(con($dbaddr,$dbname,$dbid,$dbpass))
    {
         $flag=true;
            $tbl=' ';
            $qry='show tables from '.$dbname; // get list of Tables from the database
            $res=mysql_query($qry);
            for($i=0;$i<mysql_num_rows($res);$i++) // fromat each tablle and store in $tbl
            {
                $table[]=mysql_result($res,$i,0);
                
                $tbl.="\n<tr>";
                $tbl.="<td><label><input type=\"checkbox\" name=\"chk$i\" />&nbsp;";
                $tbl.=mysql_result($res,$i,0);
                $tbl.="\n</label></td>";
                $tbl.="\n</tr>";
                
                $_SESSION['dbaddr']=$dbaddr;
                $_SESSION['dbname']=$dbname;
                $_SESSION['dbid']=$dbid;
                $_SESSION['dbpass']=$dbpass;
                $_SESSION['table']=$table;
            }
       
    }
    else
        $err='Database Connection Not Successful';
        
}

//Generate Forms
if(isset($_POST['btnGen']))
{   
    if(isset($_SESSION['table']))
        $table=$_SESSION['table'];
    
    if(1) // If Database Connection has been made
    {
        if(con($_SESSION['dbaddr'],$_SESSION['dbname'],$_SESSION['dbid'],$_SESSION['dbpass'])) //Database Re-Connect
        {
            // Check which tables have been selected to be generated        
            for($i=0;$i<count($table);$i++) 
            {
                if(isset($_POST['chk'.$i]))
                {
                     
                     // HEADER START //
$fp='
<!DOCTYPE HTML>
<html>
    <head>
        <title>'.ucfirst($table[$i]).'</title>
        <link rel="stylesheet" href="#" media="screen"/>
        <style type="text/css">
            body
            {
            	font-family:arial;
            	font-size:0.85em;
                color:#4F4F4F;
                width:960px;
                margin:0 auto;
                margin-top:10px;
            }
            td{
                padding:2px 5px;
            }

        </style>   
        
    </head>
    <body>
        <form id="frm" name="frm" method="post">
            <h3>'.$table[$i].'</h3>
            <br />
            <table>';                    
                     // HEADER END
                     
                     
                     
                     
                     
                     
                    $qry1="desc ".$table[$i];
                    $res1=mysql_query($qry1);
                    //Get Each Field
                    for($j=0;$j<mysql_num_rows($res1);$j++)
                    {
                        $fld_name=mysql_result($res1,$j,0);
                        $type="text";
                        
                        if($fld_name=='password' || $fld_name=='pass' || $fld_name=='upass' || $fld_name=='u_pass')
                        {
                            $fld_name="Password ";
                            $type="password";
                        }
                            
                        // CONTENT START
$fp.='
            <tr> 
                <td>'.ucfirst($fld_name).'</td>
                <td><input type="'.$type.'" name="inp_'.$fld_name.'" /></td>
            </tr>
                                                
';
                        
                        if($type=='password')
$fp.='
            <tr> 
                <td>Confirm password</td>
                <td><input type="'.$type.'" name="inp_c_'.$fld_name.'" /></td>
            </tr>
                                                                        
                        ';
                        // CONTENT END
                    
                    }                  
                                        
                    
                    
                    
                    
                    
                     // FOOTER START 
$fp.='
                <tr>
                    <td style="text-align:right;" colspan="2">
                        <input type="submit" name="btn" value="Submit" />
                    </td>
                </tr> 
            </table>
                
        </form>
    </body>
</html>  
                ';
                    // FOOTER END
                    
                    
                    
                    //Writing file
                    $fn=$_SESSION['dbname'].'_'.$table[$i].'.htm';
                    $fl=fopen($fn,'w') or die('can not open the file');
                    fwrite($fl,$fp);
                    fclose($fl);
                }
                
            }
            
            
        }
    }
    
}

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>phpCRUD</title>
        <style type="text/css">html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-style:normal;letter-spacing:normal;vertical-align:baseline;border-collapse:separate}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content: ' ';content:none}table{border-collapse:collapse;border-spacing:0}@import url( 'reset.css ');.clr{clear:both}body{font-family:arial;font-size:0.85em;color:#4F4F4F;width:960px;margin:0 auto}#header{height:50px;padding:10px 2px;border-bottom:1px solid #BDBDBD;margin-bottom:15px}#logo a{text-decoration:none;color:black}#logo{text-shadow:0 0 15px #D6D6D6;float:left;padding-top:20px;-webkit-transition:all 400ms ease-in;-moz-transition:all 400ms ease-in;-ms-transition:all 400ms ease-in;-o-transition:all 400ms ease-in;transition:all 400ms ease-in}#logo:hover{text-shadow:0 0 15px #17B9F9}#logo #logo1{font-size:2em;font-style:italic;color:#92979A}#logo #logo2{font-size:3em;font-weight:bold;color:#0B609D}#banner{margin-left:150px;text-align:center}#content{}#content td{padding:2px 5px}#selectTable #tblContainer{height:200px;width:300px;overflow:auto;padding:10px;margin:2px;border-radius:5px}#selectTable #tblContainer td{padding:5px 10px}#selectTable #tblContainer tr{border:1px solid #C8C8C8}#selectTable #tblContainer tr:hover{background-color:#F9EDCC;color:#2C2C2C}#footer{border-top:1px solid gray;margin:10px 0;padding:10px 2px;color:#898989;font-size:0.87em;font-style:italic}</style>
    </head>
    <body onload="chkChk()">
     <form id="frm" name="frm" method="post">
        <div id="header">
            <div id="logo"><a href="form_generator.php" ><span id="logo1">php</span><span id="logo2">CRUD</span></a></div>
            <div id="banner">
                <h1><i>php</i><b>Crud</b> - Generate Web Forms, tables from mySQL database in fastest and easiest way.</h1>
            </div>
            <div class="clr"></div>
        </div>
        <div id="content">
            <div id="msg">
                <?php echo $err; ?>
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
                <h3><b><i><?php echo $dbname; ?></i></b> tables</h3>
                <div id="tblContainer">
                    <table>
                        <?php echo $tbl; ?>                       
                    </table>
                </div>
                <label><input type="checkbox" id="chkAll" name="chkAll" onclick="checkAll(this)"  /> Check All</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Generate tables" name="btnGen" />
            </div>
            <div id="test">
               
            </div>      
           </div>
        <div id="footer">
            &copy; 2012, theCodeCult. All Rights Reserved.
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
      
    </body>
</html>