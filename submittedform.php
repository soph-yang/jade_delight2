<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <!-- CONNECTING TO DATABASE SERVER AND ADDING DATA TO HTML TABLE -->
        <?php 
            $server = "sql200.epizy.com";
            $userid = "epiz_27903401";
            $pw = "qvXEl6THspCiW";
            $db = "epiz_27903401_jade_delight";
            
            //create connection
            $conn = new mysqli($server, $userid, $pw);
            
            //check connection 
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            //select the database 
            $conn->select_db($db);
            
            //run a query 
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
        ?>
        
        <!-- GETTING AND DISPLAYING FORM INFO -->
        <?php
            if ($result->num_rows > 0) {
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    $itemcost = $_GET["cost"][$i];
                    $quan = $_GET["quan$i"];
                    $name = $row["name"];
                    $i++;
                    
                    echo "$quan $name: $$itemcost <br/>";
                }
            } else {
                echo "no results";
            }

            //close database connection 
            $conn->close();
            
            $subtotal = $_GET['subtotal'];
            $tax = $_GET['tax'];
            $total = $_GET['total'];
            
            $str_total = "<br/>Subtotal: $$subtotal <br/> Tax: $$tax<br/> Total: $$total <br/>";
            echo $str_total;
            
            if ($_GET['p_or_d'] == "pickup") {
                $deliverytime = 15;
                $delivery_str = "Your order will be ready for pick up at ";
            } else if ($_GET['p_or_d'] == "delivery") {
                $deliverytime = 30;
                $delivery_str = "Your order will be delivered to you at ";
            }
            $time = date("h:ia", strtotime("+$deliverytime Minutes"));
            echo "Thank you for your order! Your order will be ready at $time.";


            // <-- SENDING EMAIL -->
            $email_address = $_GET['email'];
            $email_str = "Thank you for ordering Jade Delight! Your order total is $$total. $delivery_str $time.";

            mail($email_address, "Your Jade Delight Order", $email_str);
        ?>
    </body>
</html>