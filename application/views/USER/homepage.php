<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<main id="main" class="main">

  <div class="pagetitle">
  <h1 class="card-title">Analizza un nuovo sito</h1>

  </div>

  <section class="section">
    <div class="row">
      <div class="lg-6">

        <div class="card">
          <div class="card-body">
            <div class="card-header">
              <h4 class="card-title"><b>ANALIZZA SITO WEB</b></h4>
            </div>
            <br>
            <p>Inserisci un link di qualsiasi pagina del sito web che vuoi analizzare. Verrà creata una <b>directory</b> dedicata e analizzata la presenza di <b>sitemap</b>.</p>
            <br>
         
            <form action="#">
              <div class="form-floating mb-3">

                <input type="url" class="form-control" id="url_to_scrape" placeholder="name@example.com" required>
                <label for="floatingInput">Link del sito web da analizzare</label>
                <br>
                <div id="new_website_state">
                  <button type="button" class="btn btn-primary" id="new_website">Analizza</button>
                </div>
              </div>

            </form>

          </div>
        </div>
        <br>
        <div class="card">
          <div class="card-body">
          <div class="card-header">
              <h4 class="card-title"><b>ULTIMI SITI WEB ANALIZZATI</b></h4>
            </div>

            <div style="overflow-x:auto;">
            <table class="table table-hover table-borderless" id="">
              <thead>
                <tr>
                  <th></th>
                  <th scope="col">Nome del sito</i></i></th>
                  <th scope="col">Data di analisi</i></th>
                  <th scope="col">Vedi dettagli di analisi</th>


                </tr>
              </thead>
              <tbody>

                <?php
                if ($recent_user_sites){

                foreach ($recent_user_sites as $site) :
                ?>
                   <tr class="item">
                  <td><i id = "<?= $site->id_site?>" favorites = "<?= $site->preferiti?>" class= "favorite fav-homepage bi <?php echo ($site->preferiti == 0) ? "bi-heart" : 'bi-heart-fill' ?>" ></i></td>
                  <td><?= $site->site_name?></td>
                  <td><?= date('d/m/Y', strtotime($site->fetch_date)) ?></td>
                  <td><a href = "<?= site_url("Tool/dashboard?site=" . $site->site_name)?>"><button type="button" class="btn btn-primary"><i class="bi bi-plus-circle"></i></button></a></td>

                </tr>

                <?php
                endforeach;
              }
                ?>

               
              </tbody>
            </table>
            </div>
       

          </div>
        </div>

      </div>

</main>