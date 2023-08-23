 <?php

  $con = mysqli_connect("localhost", "root", "", "form") or die($mysqli_error($con));

  $name = isset($_POST["name"]) ? $_POST["name"] : "";
  $name = mysqli_real_escape_string($con, $name);

  $contact = isset($_POST['contact']) ? $_POST['contact'] : "";
  $contact = mysqli_real_escape_string($con, $contact);

  $email = isset($_POST['email']) ? $_POST['email'] : "";
  $email = mysqli_real_escape_string($con, $email);

  $subject = isset($_POST['subject']) ? $_POST['subject'] : "";
  $subject = mysqli_real_escape_string($con, $subject);

  $message = isset($_POST['message']) ? $_POST['message'] : "";
  $message = mysqli_real_escape_string($con, $message);

  if (isset($_POST['submit'])) {

    $regex_name =  "/^[a-zA-z ]*$/";
    $regex_email = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
    $regex_num = "/^[789][0-9]{9}$/";

    $e = "SELECT * FROM `contact_form` WHERE email='$email'";
    $em = mysqli_query($con, $e) or die($mysqli_error($con));
    $c = "SELECT * FROM `contact_form` WHERE contact='$contact'";
    $co = mysqli_query($con, $c) or die($mysqli_error($con));
    $error = array();

    if (empty($name) || empty($contact) || empty($email) || empty($subject) || empty($message)) {
      $error['empty'] = "<span class='red'>Please fill all the fields</span>";
    } else {
      if (!preg_match($regex_name, $name)) {
        $error['validate'] = "<span class='red'>Not a valid name</span>";
      } elseif (mysqli_num_rows($em) > 0 && mysqli_num_rows($co) > 0) {
        $error['duplicate'] = "<span class='red'>Email Already Exists</span>";
        $error['dup'] = "<span class='red'>Contact Already Exists</span>";
      } else if (mysqli_num_rows($em) > 0) {
        $error['duplicate'] = "<span class='red'>Email Already Exists</span>";
      } else if (mysqli_num_rows($co) > 0) {
        $error['dup'] = "<span class='red'>Contact Already Exists</span>";
      } else if (!preg_match($regex_email, $email)) {
        $error['validation'] = "<span class='red'>Not a valid Email Id</span>";
      } else if (!preg_match($regex_num, $contact)) {
        $error['valid'] = "<span class='red'>Not a valid phone number</span>";
      } else {
        $query = "INSERT INTO `contact_form`(name, contact, email, subject, message , IP_Address)VALUES('" . $name . "','" . $contact . "','" . $email . "','" . $subject . "','" . $message . "','" . $_SERVER['REMOTE_ADDR'] . "')";
        if (mysqli_query($con, $query) or die(mysqli_error($con))) {

          $to = $email;
          $subject = $subject;
          $message = $message;
          $from = "dy1883078@gmail.com";
          $header = "From: " . $from . "\r\n";

          $retval = mail($to, $subject, $message, $header);
          if ($retval == true) {
            $error['success'] = "<span class='green'>Form Submitted Successfully and mail sent Successfully</span>";
          } else {
            $error['success'] = "<span class='green'>Form Submitted Successfully but mail could not be Sent</span>";
          }

          $name = "";
          $contact = "";
          $email = "";
          $subject = "";
          $message = "";
        }
        mysqli_close($con);
      }
    }
  }
  ?>
 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
 </head>

 <body>

   <div class="contain">
     <h1>Contact Form</h1>
     <form method="post">
       <div class="container">
         <?php if (isset($ret)) {
            echo $ret;
          } ?>
         <div class="nam">
           <label class="name" for="fname">Full Name:</label><br>
           <input type="text" name="name" value="<?php echo $name ?>" autocomplete="off"><br>
         </div>
         <?php if (isset($error['validate'])) {
            echo $error['validate'];
          } ?>
         <div class="conta">
           <label class="contact" for="fname">Phone Number:</label><br>
           <input type="text" name="contact" value="<?php echo $contact ?>" autocomplete="off"><br>
         </div>
         <?php if (isset($error['dup'])) {
            echo $error['dup'];
          } ?>
         <?php if (isset($error['valid'])) {
            echo $error['valid'];
          } ?>
         <div class="email">
           <label for="fname">Email:</label><br>
           <input type="email" name="email" value="<?php echo $email ?>" placeholder="Enter your E-mail" autocomplete="off"><br>
         </div><?php if (isset($error['duplicate'])) echo $error['duplicate']; ?>
         <?php if (isset($error['validation'])) {
            echo $error['validation'];
          } ?>
         <div class="subject">
           <label for="fname">Subject:</label><br>
           <input type="text" name="subject" value="<?php echo $subject ?>" autocomplete="off"><br>
         </div>
         <div class="message">
           <label for="fname">Message:</label><br>
           <textarea id="message" name="message" cols="3" placeholder="Any other message" autocomplete="off"><?php echo $message ?></textarea><br>
           <?php if (isset($error['empty'])) {
              echo $error['empty'];
            } ?>
           <?php if (isset($error['success'])) {
              echo $error['success'];
              sleep(5);
              header('Location:' . $_SERVER['PHP_SELF']);
            } ?>
         </div>
         <button id="submitbtn" name="submit">Submit</button>
       </div>
     </form>
   </div>

   <style>
     * {
       margin: 0;
       padding: 0;
     }

     body {
       display: flex;
       align-items: center;
       justify-content: center;
       height: 100vh;
       background-color: #4e59ad;

     }

     .red {
       color: red;

     }

     .green {
       color: green;

     }

     .contain {
       padding: 60px;
       width: 25%;
       padding-bottom: 0px;
       background-color: white;
     }

     .container {
       padding-top: 5%;
       text-align: center;
       row-gap: 11px;
       font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;

     }

     .contain h1 {
       padding-bottom: 8%;
       padding-left: 50px;
       color: black;
       font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
     }

     .container label {
       margin-right: 65%;

     }

     .contact {
       padding-left: 11px;
     }

     .container input,
     textarea {
       padding: 15px;
       margin: 15px;
       width: 80%;
       border-radius: 5px;
       font-size: 15px;
     }

     #submitbtn {
       width: 25%;
       border-radius: 11px;
       margin: 10px;
       padding: 10px;
       border-color: blue;
       background-color: aliceblue;
     }
   </style>
 </body>

 </html>