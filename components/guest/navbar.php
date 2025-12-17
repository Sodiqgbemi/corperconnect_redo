<body>
<header class="sticky-top">
  <div class="logo">
    <a href="../index.php"><img src="<?php echo BASE_URL; ?>/assets/images/logo5.png" alt="Logo" /></a>
    <h1 class="baskervville-sc-regular"><a href="index.php" style="text-decoration:none; color:#153b23;"></a></h1>
  </div>
  <div class="me-5">       
              <a href="../index.php" class="btn " >Home</a>
              <a href="../index.php#about" class="btn " >About</a>
            <a href="../index.php#services" class="btn ">Services</a>
                  <?php
          if(isset($_SESSION['usersonline'])){ 
         ?> 
          <a href="logout.php" class="btn btn-outline-warning">Logout</a>
               <?php
          }else{  ?>
          
           <a href="signup.php" class="btn btn-outline-warning">Sign Up</a>
           
          <?php } ?>
          
        
        </div>
</header>