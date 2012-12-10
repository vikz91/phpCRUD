<?php
                    
            //HTML HEADER START
$CRUD_FILE_PAGE='<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />    
        <title>'.$tbl[$i].' | phpCRUD</title>
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
            <h3>'.$tbl[$i].'</h3>
            <br />
            <table>';                    
                     // HTML HEADER END                     
                     
                    $qry_l1="desc ".$tbl[$i];
                    $res_l1=mysql_query($qry_l1);
                    //Get Each Field
                    for($j=0;$j<mysql_num_rows($res_l1);$j++)
                    {
                        $fld_name=mysql_result($res_l1,$j,0);
                        $fld_type="text";
                        foreach($IDF['pass'] as $psnn)
                            if($fld_name==$psnn)
                                $fld_name=$fld_type="password";
                    
                      
                        // CONTENT START
                      $CRUD_FILE_PAGE.='
                <tr> 
                    <td>'.ucfirst($fld_name).'</td>
                    <td><input type="'.$fld_type.'" name="inp_'.$fld_name.'" /></td>
                </tr>                                                
';
                        
                        if($fld_type=='password')
                        $CRUD_FILE_PAGE.='
                <tr> 
                    <td>Confirm password</td>
                    <td><input type="'.$fld_type.'" name="inp_c_'.$fld_name.'" /></td>
                </tr>
                                                                        
                        ';
                        // CONTENT END
                    
                    }                  
                                   
                    
                    
                    
                    
                    
                     // FOOTER START 
                        $CRUD_FILE_PAGE.='
                <tr>
                    <td style="text-align:right;" colspan="2">
                        <input type="submit" name="btn" value="Submit" />
                    </td>
                </tr> 
            </table>
                
        </form>
    </body>
</html> '; 
                    //FREEing Resource
                   unset($fld_name,$fld_type,$j);
                   
                   $CRUD_FILE_RESOURCE=fopen($dir_crud.$tbl[$i].'.htm','w') or die('can not open the file');
    fwrite($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);    
    fclose($CRUD_FILE_RESOURCE);
    unset($CRUD_FILE_RESOURCE,$CRUD_FILE_PAGE);
?>