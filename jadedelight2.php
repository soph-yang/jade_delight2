<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <script
          src="https://code.jquery.com/jquery-3.6.0.js"
          integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
          crossorigin="anonymous"></script>
        <title>Get Database PHP</title>
    </head>
    <body>
        <!-- JAVASCRIPT -->
        <script type="text/javascript">
            function MenuItem(name, cost)
            {
            	this.name = name;
            	this.cost = cost;
            }
                        
            menuItems = new Array();

            function makeSelect(name, minRange, maxRange)
            {
            	var t = "";
            	t = "<select name='" + name + "' size='1'>";
            	for (j = minRange; j <= maxRange; j++)
            	   t += "<option>" + j + "</option>";
            	t += "</select>";
            	return t;
            }
            
            $(document).ready(function() {
                //When a user selects a quantity the amount should automatically fill
                for (i = 0; i < menuItems.length; i++) {
                    let menuCost = menuItems[i].cost;
                    let tableRow = $('.itemtable tr').eq(i+1);
                    
                    tableRow.change(function() {
                        //change text input value 
                        let selectedAmt = $(this).find(':selected').text();
                        let totalCost = (selectedAmt * menuCost).toFixed(2);
                        tableRow.find('input[name="cost[]"]').val(totalCost);
                        
                        //add up all the total costs for each item
                        let subtotal = 0;
                        $('input[name="cost[]"]').each(function() {
                            subtotal += parseFloat($(this).val());
                        });
                        //change subtotal, tax, and total input value
                        $('#subtotal').val(subtotal);   
                        tax = (subtotal*0.0625).toFixed(2)
                        $('#tax').val(tax); 
                        total = (parseFloat(tax) + subtotal).toFixed(2);
                        $('#total').val(total);
                    });
                }
                   
                //street and city fields hidden unless the user selects delivery
                $('input[value="delivery"]').click(function() {
                    $('#citystreet').css("display", "block");
                })
                $('input[value="pickup"]').click(function() {
                    $('#citystreet').css("display", "none");
                })
            })

            function validate() {
                let err = false;
                
                with (document.data) {
                    //last name and phone must be entered
                    let phonenum = //allow inputs number with dashes and parenthesis
                    phone.value.replaceAll("-", "").replaceAll("(", "").replaceAll(")", ""); 
                    if ((lname.value == "") || (phone.value == "") || (email.value == "")) {
                        alert("You did not enter a last name, email, and/or phone");
            			lname.focus();
            			err = true;
                    //phone number must be valid 
            		} else if (phonenum.length != 10 || isNaN(phonenum)) { 
                        alert("Please enter a valid phone number");
                        err = true;
                    }
                    
                    //street and city must be entered
                    if (p_or_d[1].checked) {
                       if ((street.value == "") || (city.value == "")) {
                           alert("You did not enter a street and/or city");
                           err = true;
                       }
                    }
                    
                    //select at least one item
                    if($('#total').val() == 0) {
                        alert("Please select at least one item");
                        err = true;
                    } 
                }   
                
                return !err;
            }
        </script>
        
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
     
            //put results in menuItems array using PHP
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $name = $row["name"];
                    $cost = $row["cost"];
                    echo "<script type='text/javascript'>
                        menuItems.push(new MenuItem('$name', $cost))
                    </script>";
                }
            } else {
                echo "no results";
            }

            //close the connection 
            $conn->close();
        ?>
        
        <!-- HTML FOR CREATING FORM -->
        <h1>Jade Delight</h1>
        <form
            name="data"
            method="get" 
            action="http://circlebreather.epizy.com/submittedform.php"
            onsubmit="return validate()" 
        >
            <p>First Name: <input type="text"  name='fname' /></p>
            <p>Last Name*:  <input type="text"  name='lname' /></p>
            <p>Email*:  <input type="text"  name='email' /></p>
            <div id="citystreet" style="display: none">
                <p>Street: <input type="text"  name='street' /></p>
                <p>City: <input type="text"  name='city' /></p>
            </div>
            <p>Phone*: <input type="text"  name='phone' /></p>
            <p> 
            	<input type="radio"  name="p_or_d" value="pickup"  checked="checked"/>Pickup  
            	<input type="radio"  name="p_or_d" value="delivery"/>Delivery
            </p>
            
            <table class="itemtable" border="0" cellpadding="3">
                <tr>
                    <th>Select Item</th>
                    <th>Item Name</th>
                    <th>Cost Each</th>
                    <th>Total Cost</th>
                </tr>
                <script language="javascript">
                    var s = "";
                    for (i = 0; i < menuItems.length; i++)
                    {
                        s += "<tr><td>";
                        s += makeSelect("quan" + i, 0, 10);
                        s += "</td><td>" + menuItems[i].name + "</td>";
                        s += "<td> $ " + menuItems[i].cost.toFixed(2) + "</td>";
                        s += "<td>$<input type='text' name='cost[]' value='0'/></td></tr>";
                    }
                    console.log(s);
                    document.write(s);
                </script>
            </table>

            <p>Subtotal: 
                $ <input type="text"  name='subtotal' id="subtotal" value='0' />
            </p>
            <p>Mass tax 6.25%:
                $ <input type="text"  name='tax' id="tax" value='0' />
            </p>
            <p>Total: 
                $ <input type="text"  name='total' id="total" value='0' />
            </p>

            <input type="submit" value="Submit Order" />
        </form>
    </body>
</html>