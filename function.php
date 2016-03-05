<?php    
        
require 'db.php';     

// variables     
$action =    $_REQUEST['action']; 
$item =    $_REQUEST['item']; 
$ip = $_SERVER['REMOTE_ADDR'];

        
        
// hook for ajax    
if($action=='add' ) {    
    if($item=='') 
        print('заполните имя'); 
    else 
        add($_POST['item']); //add to db    
};    

function add($item){ // ф-¤ добавлени¤ записей    
    global $conn; // глобальное подключение к базе    

    $query = "INSERT INTO Items ( `item`) VALUES ('$item')"; 
    mysql_query($query, $conn);

} // END >> add
if($action=='get'){
    $query = "SELECT * FROM Items ORDER BY id desc limit ".$_REQUEST['limit']; 
    $results = mysql_query($query, $conn); 
    if(mysql_num_rows($results))
    {
        $out="";
        while($row = mysql_fetch_assoc($results)){
            $out.= "<div class = \"item_container\" item_id=".$row['id'].">
            <div class=\"items\" contenteditable=\"false\">".$row['item']."</div>
            <button class=\"remove\">Remove</button>
            <button class=\"edit\">Edit</button>
            </div>";
        };
    }
    print($out);
} // END >> get

if($action=='set_edit') {
    $query = "SELECT editing, editor_ip FROM Items WHERE id = ".$_REQUEST['item'];
    $res = mysql_query($query);
    $row = mysql_fetch_assoc ($res);
    $edition_allow = 2;

    if ($row['editing'] == 1) {
        if ($row['editor_ip'] == $_SERVER['REMOTE_ADDR']){
            $edition_allow=1;
        }else{
            $edition_allow=0;
        }
    }else{
        $edition_allow=1;
    }


    if ($edition_allow){
        $query = "UPDATE Items SET editor_ip='".$_SERVER['REMOTE_ADDR']."', editing='1', start_edit_date=NOW() WHERE id='".(int)$_REQUEST['item']."'";
        mysql_query($query);
        if(!mysql_error()){
           echo 1; //редактирование разрешено
        }else{
           echo mysql_error();
        }
    }else{
        echo 0; // редактирование запрещено
    }
} // END >> set_edit

if($action=='edit'){
    $query = "UPDATE `Items` SET item='".strip_tags($_REQUEST['text'])."',editing=0 WHERE id =".(int)$_REQUEST['item']." ";
    mysql_query($query, $conn); 
}

if($action=='remove'){
    $query = "DELETE from `Items` WHERE id = ".$_REQUEST['item']."";    
    $result = mysql_query($query, $conn);
}

?>