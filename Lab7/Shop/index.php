<?php 
    session_start();
    header('Content-Type: text/html; charset=utf-8'); 
    $basket_var = 'basket';

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

	$db->autocommit(FALSE);
	
    $result = $db->query($query);


    //add to basket
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        //check if item has been added
        if(isset($_POST["name"]))
        {
            $stmt = $db->prepare("select nazwa, cena, liczba_w_magazynie from produkty where nazwa=?");
            $stmt->bind_param('s', $_POST["name"]);
            $stmt->execute();
            $item = $stmt->get_result()->fetch_array();
            
            if(empty($item))
            {
                //if object from user was not found, redirect to shop by GET
                header("Location:index.php");
            }
                

            $item = array(
                'id' => $item["nazwa"],
                'name' => $item['nazwa'],
                'price' => $item['cena'],
                'max_quantity' => $item['liczba_w_magazynie']
            ) ;
            
            if(!array_key_exists($basket_var, $_SESSION) )
            {
                $_SESSION[$basket_var] = array();
            }

            if( array_key_exists($item['id'], $_SESSION[$basket_var]) ){
                if($_SESSION[$basket_var][$item['id']]['quantity'] + 1 <=  $_SESSION[$basket_var][$item['id']]['max_quantity'])
                {
                    $_SESSION[$basket_var][$item['id']]['quantity'] +=1;
                    unset($_SESSION['item_overflow']);
                }
                else
                {
                    $_SESSION["item_overflow"] = $_SESSION[$basket_var][$item['id']];
                }
                
            }
            else{
                if($item['max_quantity'] > 0)
                {
                    $item['quantity'] = 1;
                    $_SESSION[$basket_var][$item['id']]  = $item;
                }
            }


            //remove item from post array
            unset($_POST["item"]);
        }
        //GET to client
        header("Location:index.php");
    }
?>

<!-- header -->
<!DOCTYPE html>
<html lang="pl" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <title>Zaawansowany CSS</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css" title="Arkusz stylów CSS" />
    <script type="text/javascript" src="scripts/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="scripts/async-post.js"></script>
</head>
<body>
    <div class="grid-container">
        <header>
            <div class="vertical_center">
                <!-- <img alt="Logo strony" src="resources/logo.png" class="logo" /> -->
            </div>
            <h1 class="vertical_center">
                Prosty sklep
            </h1>
            <div class="links vertical_center">
                <nav class="main-nav">
                    <!-- <a href="">Wyczyść koszyk</a>
                    <img alt="menu" src="resources/menu.png" class="smallMenu" /> -->
                    <input type="button" name="clear-basket" class="button" onClick="send_request('clear', 'basket-list')" value="Wyczyść" />
                    <input type="button" name="buy-basket" class="button" onClick="send_request('buy', 'basket-list')" value="Kup" />
                </nav>
            </div>
        </header>
<!-- end header -->


        <main>
            <p id="error-basket-msg">            
                <?php
                    if(isset($_SESSION['item_overflow']) ){
                            echo "Liczba sztuk produktu ".
                                $_SESSION['item_overflow']['name'].' na magazynie wynosi '.
                                $_SESSION['item_overflow']['max_quantity'];
                    }
                    
                ?>
            </p>

            <!-- dialog modal -->
            <div id="dialog-modal" title="Wiadomość">
            <p>
                <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
                <p id="dialog-message"></p>
            </p>
            </div>

            <!-- user's basket -->
            <div id="basket" class="first bordered">
                <div class="title bordered">
                    Koszyk
                </div>
                <div id="basket-list">
               
<?php
    if(!isset($_SESSION[$basket_var]))
    {
        echo "<p class=\"empty-basket-header\">Koszyk jest pusty</p>\n";
    }else
    {
        echo "
            <table id=\"basket-table\">
            <thead>
                <tr>
                    <td>Nazwa</td>
                    <td>Cena jednostkowa</td>
                    <td>Liczba</td>
                    <td>Cena</td>
                    <td>Usuń</td>
                </tr>
            </thead>
            <tbody>
        ";
        foreach ($_SESSION[$basket_var] as $basket_item):
            echo "<tr id='".str_replace(' ', '_', $basket_item["id"])."'>\n".
            "           <td class=\"itemHeader-table\">".$basket_item["name"]."</td>\n".
            "           <td class=\"itemPrice-table\">".$basket_item["price"]." zł</td>\n".
            "           <td class=\"itemQuantity-table\">".$basket_item["quantity"]." </td>\n".
            "           <td class=\"itemQuantity-table\">".round($basket_item["quantity"]*$basket_item["price"], 2)." </td>\n".
            "           <td><img src=\"resources/delete.png\" id=\"basket-table-remove-img\" onClick=\"send_request('remove', '".$basket_item["id"]."')\" /></td>".
            "</tr>\n"; 
        endforeach;
    
        echo "
            </tbody>
            </table>
        ";
    }
?>
                </div>
            </div>
            
            <!-- items to buy -->
<?php 
foreach ($result as $v):
    if($v["liczba_w_magazynie"] > 0)
    {
        echo
        "<div class=\"bordered\">\n".
        "   <form action=\"index.php\" method=\"POST\">\n".
        "       <input type=\"hidden\" name=\"name\" value=\"".$v["nazwa"]."\" />\n".
        "           <p class=\"title\"><input id=\"add-item-to-basket-image\" type=\"image\" name=\"submit\" src=\"resources/add.png\" border=\"0\" alt=\"Submit\" /></p>\n".
        "           <p class=\"content\">\n".
        "               <p class=\"itemHeader\">".$v["nazwa"]."</p>\n".
        "               <p class=\"itemPrice\">".$v["cena"]." zł</p>\n".
        "               <p class=\"itemQuantity\">Liczba w magazynie ".$v["liczba_w_magazynie"]." .</p>\n".
        "           </p>\n".
        "   </form>\n".
        "</div>\n";
        
    }
endforeach;
?>
        </main>

<!-- footer -->
    </div>
<footer>
        Projekt i przygotowanie strony <br />
        <b>Jakub Tomczak</b>
    </footer>
</body>
</html>
<!-- end footer -->