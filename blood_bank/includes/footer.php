<div style="position: relative; overflow: hidden; margin-top: 120px;">
  
  <!-- Innovative Floating Newsletter Card -->
  <div class="container" style="position: relative; z-index: 10; margin-bottom: -80px;">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="newsletter-card shadow-lg p-5" style="background: linear-gradient(135deg, #e63946, #b0101d); color: white; border-radius: 20px; position: relative; overflow: hidden;">
          <div style="position: absolute; top: -30px; right: -20px; opacity: 0.1; transform: rotate(-15deg);">
            <i class="fas fa-heartbeat" style="font-size: 18rem;"></i>
          </div>
          <div class="row align-items-center" style="position: relative; z-index: 2;">
            <div class="col-md-7 mb-4 mb-md-0 text-center text-md-left">
              <h2 class="font-weight-bold mb-2" style="font-size: 2.2rem;">Ready to Save a Life?</h2>
              <p class="m-0" style="opacity: 0.9; font-size: 1.1rem;">Join our network and receive emergency alerts for blood needs in your area.</p>
            </div>
            <div class="col-md-5">
              <form action="#" method="post" class="d-flex" style="background: white; border-radius: 50px; padding: 5px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                <input type="email" class="form-control border-0 bg-transparent" placeholder="Enter your email" required style="padding: 15px 25px; outline: none; box-shadow: none;">
                <button class="btn subscribe-btn rounded-pill px-4 font-weight-bold" type="submit" style="background-color: #1d3557; color: white; border: none;">Subscribe</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SVG Wave Separator -->
  <svg viewBox="0 0 1440 120" style="display: block; width: 100%; height: auto; position: relative; z-index: 1;">
    <path fill="#1d3557" fill-opacity="1" d="M0,64L60,74.7C120,85,240,107,360,101.3C480,96,600,64,720,48C840,32,960,32,1080,42.7C1200,53,1320,75,1380,85.3L1440,96L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z"></path>
  </svg>

  <footer style="background-color: #1d3557; color: #ffffff; position: relative; z-index: 1; padding-top: 60px;">
    <div class="w3ls-footer-grids pb-4">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
            <h2 class="mb-4">
              <a href="index.php" class="text-white font-weight-bold" style="text-decoration: none; font-size: 2.2rem; letter-spacing: -1px;">
                <span style="color: #e63946;">Blood</span>Bank
                <i class="fas fa-tint ml-1" style="color: #e63946;"></i>
              </a>
            </h2>
            <p class="text-light pr-lg-4" style="line-height: 1.8; opacity: 0.75; font-size: 0.95rem;">
              “One donation can save up to three lives.” Join our global initiative to ensure that no one ever suffers from a shortage of blood during emergencies.
            </p>
            <div class="mt-4 pt-2">
              <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
              <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
              <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6 mb-5 mb-lg-0 pl-lg-5">
            <h3 class="mb-4 text-white font-weight-bold" style="font-size: 1.4rem;">Quick Links</h3>
            <ul class="list-unstyled mt-3">
                <li class="mb-3"><a href="index.php" class="text-light quick-link">Home</a></li>
                <li class="mb-3"><a href="search-donor.php" class="text-light quick-link">Find Donors</a></li>
                <li class="mb-3"><a href="sign-up.php" class="text-light quick-link">Become Donor</a></li>
                <li class="mb-3"><a href="contact.php" class="text-light quick-link">Contact</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-12">
            <h3 class="mb-4 text-white font-weight-bold" style="font-size: 1.4rem;">Contact Info</h3>
            <ul class="list-unstyled mt-3">
              <?php
              $sql = "SELECT * from tblcontactusinfo";
              $query = $dbh->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              foreach ($results as $result) { ?>
                  <li class="mb-4 d-flex align-items-center">
                    <i class="fas fa-map-marker-alt mr-3 text-danger"></i>
                    <span class="text-light"><?php echo $result->Address; ?></span>
                  </li>
                  <li class="mb-4 d-flex align-items-center">
                    <i class="fas fa-phone-alt mr-3 text-danger"></i>
                    <span class="text-light"><?php echo $result->ContactNo; ?></span>
                  </li>
              <?php } ?>
            </ul>
          </div>
        </div>
        
        <div class="border-top mt-5 pt-4 text-center">
          <p class="text-light m-0" style="opacity: 0.6;">&copy; <span id="year"></span> Blood Bank. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
</div>

<style>
  .newsletter-card { transition: transform 0.4s ease; }
  .newsletter-card:hover { transform: translateY(-10px); }
  .social-icon { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); color: white; border-radius: 50%; margin-right: 10px; transition: 0.3s; }
  .social-icon:hover { background: #e63946; color: white; transform: translateY(-3px); }
  .quick-link { transition: 0.3s; text-decoration: none; opacity: 0.8; }
  .quick-link:hover { opacity: 1; padding-left: 5px; color: #e63946 !important; }
</style>

<script>document.getElementById('year').textContent = new Date().getFullYear();</script>