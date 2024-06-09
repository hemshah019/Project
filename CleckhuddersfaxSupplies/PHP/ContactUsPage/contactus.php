<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="contactus.css">
    <link rel="stylesheet" href="../HeaderPage/head.css">
    <link rel="stylesheet" href="../FooterPage/footer.css">
</head>
<body>
  <div><?php include('../HeaderPage/head.php');?></div>
    <section>
        <div class="contact">
          <div class="inner-section">
              <h1>Contact Us</h1>
              <form action="https://api.web3forms.com/submit" method="POST">
                <input type="hidden" name="access_key" value="bf145c71-49de-447c-96ef-d08f4c0ca167">
                <div class="input-data">
                  <input type="text" name="name" required>
                  <div class="underline"></div>
                  <label for="">Enter Your Name</label>
                </div>
                <div class="input-data">
                  <input type="text" name="address" required>
                  <div class="underline"></div>
                  <label for="">Enter Your Address</label>
                </div>
                <div class="input-data">
                  <input type="text" name="email" required>
                  <div class="underline"></div>
                  <label for="">Enter Your Email</label>
                </div>
                <div class="input-data textarea">
                  <textarea name="message" required></textarea>
                  <br />
                  <div class="underline"></div>
                  <label for="">Write your message</label>
                  <br />
                  <div class="skills">
                    <button>Submit</button>
                  </div>
                </div>
              </form>
          </div>
        </div>
        <div class="cont-banner">
          <div class="boxx">
            <i class="fa-solid fa-phone"></i>
            <h3>Call Us</h3>
            <p>+1 981223XXXX</p>
            <p>+1 981223XXXX</p>
          </div>
          <div class="boxx">
            <i class="fa-solid fa-location-dot"></i>
            <h3>Location</h3>
            <p>abc, abc road, uk</p>
          </div>
          <div class="boxx">
            <i class="fa-solid fa-envelope"></i> 
            <h3>Email</h3>
            <p>cleckhsupplies@gmail.com</p>
          </div>
        </div>
    </section>

    <?php include('../FooterPage/footer.php');?>
    <script src="../HeaderPage/head.js"></script>
</body>
</html>
