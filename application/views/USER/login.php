<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<body>

<main>
  <div class="container">

    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <a href="#" class="logo login d-flex align-items-center w-auto">
                <img src="<?php echo $this->config->item('risorse')['img'] ?>/seogull.png" alt="">
                <span class="d-none d-lg-block">SEOgull</span>
              </a>
            </div>

            <div class="card mb-3">

              <div class="card-body">

                <div class="pt-4 pb-2">
                  <h5 class="card-title text-center pb-0 fs-4">Accedi al tuo account</h5>
                  <p class="text-center small">Inserisci username e password per accedere</p>
                </div>

                <form class="row g-3 needs-validation" role="form" method="post" action="<?= (site_url('access/login')) ?>">

                  <div class="col-12">
                    <label for="yourUsername" class="form-label">Username</label>
                    <div class="input-group has-validation">
                      <input type="text" name="username" class="form-control" id="yourUsername" required>
                      <div class="invalid-feedback">Please enter your username.</div>
                    </div>
                  </div>

                  <div class="col-12">
                    <label for="yourPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="yourPassword" required>
                    <div class="invalid-feedback">Please enter your password!</div>
                  </div>

                  <div class="col-12">

                  </div>
                  <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                  </div>
                  <div class="col-12">
                    <p class="small mb-0">Non hai ancora un account? <a href="<?= (site_url('access/registrazione')) ?>">Creane uno subito!</a></p>
                  </div>
                </form>

              </div>
            </div>

            <div class="credits">
             
              Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>

          </div>
        </div>
      </div>

    </section>

  </div>
</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
