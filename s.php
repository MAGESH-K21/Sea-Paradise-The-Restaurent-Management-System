<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Transaction Form</title>
	<link rel="stylesheet" href="./styles.css">

</head>
<body style=" background-image: url('images/img/main.jpeg');background-size:inherit;background-repeat:no-repeat;">

<form action="./cashpayment.php" method="POST"  style="margin:auto;padding-top:100px;">
<div class="wrapper">
    <div class="title" style="color:red;">
      Transaction Form
    </div>
    <div class="form">
       <div class="inputfield">
          <label>Name</label>
          <input type="text" name = "name" class="input" required>
       </div>
      
        <div class="inputfield">
          <label>confirm order</label>
          <div class="custom_select">
            <select name="co" required>
              <option value="" >Select</option>
              <option value="yes">yes</option>
              <option value="no">no</option>
            </select>
          </div>
       </div> 
        <div class="inputfield">
          <label>Email Address</label>
          <input type="text" class="input" name="email" required>
       </div> 
      <div class="inputfield">
          <label>Phone Number</label>
          <input type="text" class="input" name="mobile" required>
       </div> 
      
      <div class="inputfield">
        <input type="submit" value="PAY NOW" class="btn" style="background:red;">
      </div>
    </div>
</div>	
</form>
	
</body>
</html>