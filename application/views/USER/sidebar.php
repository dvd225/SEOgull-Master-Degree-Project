<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="<?= site_url("Tool") ?>" class="logo d-flex align-items-center">
        <img src="<?php echo $this->config->item('risorse')['img'] ?>/seogull.png" alt="logo">
        <span class="d-none d-lg-block">SEOgull</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>



    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li>

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-danger badge-number" id="notifica_alert"></span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" id="notifica_content">


            <li class="dropdown-header">


              <p>Nessuna nuova notifica.</p>

            </li>

          </ul>

        </li>



        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $this->config->item('risorse')['img'] ?>/profile.png" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2" id="logged_username"><?= $_SESSION["username"] ?></span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= $_SESSION["fullname"] ?></h6>
              <span><?= $_SESSION["azienda"] ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?= site_url("Tool/profile") ?>">
                <i class="bi bi-person"></i>
                <span>Profilo</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?= (site_url('access/logout')) ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Log Out</span>
              </a>
            </li>

          </ul>
        </li>

      </ul>
    </nav>

  </header>

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?= ($active_link_sidebar == "analizza_nuovo_sito") ? "" : "collapsed" ?>" href="<?= site_url("Tool") ?>">
          <i class="bi bi-zoom-in"></i>
          <span>Analizza un nuovo sito</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-heart-fill"></i><span>Siti Web Preferiti</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

          <?php
          if ($user_sites) {
            foreach ($user_sites as $site) :
              if ($site->preferiti == 1) {
          ?>
                <li>
                  <a href="<?= site_url("Tool/dashboard?site=" . $site->site_name) ?>">
                    <i class="bi bi-circle-fill"></i><span><?= $site->site_name ?></span>
                  </a>
                </li>

          <?php
              }
            endforeach;
          }
          ?>




        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($active_link_sidebar == "dashboard") ? "" : "collapsed" ?>" href="<?= site_url("Tool/dashboard") ?>">
          <i class="bi bi-pie-chart-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>


      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link <?= ($active_link_sidebar == "profilo") ? "" : "collapsed" ?>" href="<?= site_url("Tool/profile") ?>">
          <i class="bi bi-person"></i>
          <span>Profilo</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q.</span>
        </a>
      </li>



    </ul>

  </aside>