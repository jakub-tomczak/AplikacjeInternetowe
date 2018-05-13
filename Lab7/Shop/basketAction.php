<?php
    session_start();
    $basket_var = "basket";
    // set_include_path(getcwd().PATH_SEPARATOR. "."
    //     .PATH_SEPARATOR."\\"
    //     .PATH_SEPARATOR.dirname(__FILE__)."\\");
    // include 'first.php';
    // include("./database.PHP");
    // include 'database.php';
    // include(dirname(__FILE__)."/database.PHP");
    
    //connect database
    $db = new mysqli("localhost", "root", "", "sklep");
    if(!$db->set_charset("utf8"))
    {
        echo 'couldnt set utf-8';
    }
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
    $query  = "select  * from produkty;";
    
    $result = $db->query($query);

    //rewrite results to array
    $res = array();
    foreach($result as $v):
        $res[$v['nazwa']] = $v;
    endforeach;
    
    function validate_session_data($res)
    {
        $result = array();
        $result['error_msg'] = '';
        $result['new_quantities'] = array();
        $error = array();
        foreach ($_SESSION['basket'] as $v):
            if(!array_key_exists($v['name'], $res)){
                //$error[$v['name']] = 'Produkt '.$v['name'].' nie istnieje!';
                $result['error_msg'] = $result['error_msg'].'Produkt '.$v['name'].' nie istnieje!<br/><br/>\n';
                continue;
            }
            
            if($v['quantity'] > $res[$v['name']]['liczba_w_magazynie']){
                $result['error_msg'] = $result['error_msg']."Liczba sztuk produktu ".$v['name']." wynosi ".$res[$v['name']]['liczba_w_magazynie'].", wybrano ".$v['quantity'].'.<br/><br/>\n';
                //$error[$v['name']] = "Liczba sztuk produktu '".$v['name']." wynosi ".$res[$v['name']]['liczba_w_magazynie'].", wybrano ".$v['quantity'];
                continue;
            }

            $result['new_quantities'][$v['name']] = $res[$v['name']]['liczba_w_magazynie'] - $v['quantity'];
        endforeach;
        return $result;
    }

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
         //check if item has been added
         if(isset($_POST["action"])){
             // print_r($_SESSION);
             // echo 'adding item';
             switch($_POST["action"])
             {
                case "clear":
                    if(isset($_SESSION[$basket_var]))
                    {
                        unset($_SESSION[$basket_var]);
                        echo '{ "state" : "OK", "action" : "clear", "msg" : ""}';
                    }else
                    {
                        echo '{ "state" : "ERROR", "action" : "clear", "msg" : "Brak przedmiotów do usunięcia"}';
                    }
                    break;
                case "buy":
                    if(!isset($_SESSION[$basket_var]))
                    {
                        echo '{ "state" : "ERROR", "action" : "buy", "msg" : "Brak przedmiotów do kupienia"}';
                    }else
                    {
                        //remove items from basket
                        $validation_result = validate_session_data($res);
                        if(!empty($validation_result['error_msg'])){
                            echo '{ "state" : "ERROR", "action" : "buy", "msg" : "'.$validation_result['error_msg'].'"}';
                        }else
                        {
                            //update database
                            $db->autocommit(FALSE);
                            $db->begin_transaction();
                            $update_state = TRUE;
                            foreach($_SESSION[$basket_var] as $item):
                                
                                $stmt = $db->prepare("update produkty set liczba_w_magazynie=? where nazwa=?");
                                $stmt->bind_param('ds', $validation_result["new_quantities"][$item['name']], $item['name']);
                                if(!$stmt->execute())
                                {
                                    $db->rollback();
                                    echo 'dupa';
                                    echo '{ "state" : "ERROR", "action" : "buy", "msg" : "Błąd podczas aktualizacji bazy danych"}';
                                    $update_state = FALSE;
                                    break;
                                }
                            endforeach;

                            if($update_state)
                            {
                                $db->commit();
                            }else{
                                break;
                            }

                            unset($_SESSION[$basket_var]);
                            echo '{ "state" : "OK", "action" : "buy", "msg" : ""}';
                        }
                    }
                    break;
                case "remove":
                    if(isset($_POST['id']))
                    {    
                        if(isset($_SESSION[$basket_var][$_POST['id']]))
                        {
                            unset($_SESSION[$basket_var][$_POST['id']]);
                            echo '{ "state" : "OK", "action" : "remove", "msg" : ""}';
                        }else
                        {
                            echo '{ "state" : "ERROR", "action" : "remove", "msg" : "Przedmiot nie znajduje się w koszyku"}';
                        }
                        
                    }else{
                        echo '{ "state" : "ERROR", "action" : "remove", "msg" : "Nieznany przedmiot do usunięcia"}';
                    }
                    break;
             } 
             //remove item from post array
             if(isset($_POST["item"]))
             {
                unset($_POST["item"]);
             }
         }
         else
         {
            echo '{ "state" : "ERROR", "action" : "undefined", "msg" : "Nieznana akcja"}';
         }
    }
?>