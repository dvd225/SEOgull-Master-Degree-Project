<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>


<main id="main" class="main">

  <div class="pagetitle">
    <h1 class="card-title">Dashboard <?= isset($_GET['site']) ? "<span>/ </span><span id='active_site_name'> {$_GET['site']}" : '' ?></span></h1>

  </div>

  <section class="section dashboard">

    <div class="row">
      <div class="lg-12">

        <div class="card">
          <div class="card-body">
            <div class="card-header">
              <h4 class="card-title"><b>SITI WEB ANALIZZATI</b></h4>
            </div>

            
            <div style="overflow-x:auto;">
              <table class="table table-hover table-borderless datatable" id="all_sites">
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
                  if ($user_sites) {

                    foreach ($user_sites as $site) :


                  ?>
                      <tr class="item <?= (!isset($_GET['site'])) ? '' : (($site->site_name == $_GET['site']) ? "table-warning" : "") ?>">
                        <td><i id="<?= $site->id_site ?>" favorites="<?= $site->preferiti ?>" class="favorite fav-dashboard bi <?php echo ($site->preferiti == 0) ? "bi-heart" : 'bi-heart-fill' ?>"></i></td>
                        <td><?= $site->site_name ?></td>
                        <td><?= date('d/m/Y', strtotime($site->fetch_date)) ?></td>
                        <td><a href="<?= site_url("Tool/dashboard?site=" . $site->site_name) ?>"><button type="button" class="btn btn-primary"><i class="bi bi-plus-circle"></i></button></a></td>

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
    </div>

    <?php if (isset($_GET['site'])) : ?>
      <div class="" id="dettagli_site">
        <div class="row">

          
          <div class="col-lg-8">
            <div class="row">

              
              <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">



                  <div class="card-body">
                    <h5 class="card-title">Link Analizzati</h5>

                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle-fill"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $links_to_scrape[0]['url_to_scrape'] - 1 . "/{$links_to_scrape[0]['total_link']}" ?></h6>
                        <span class="text-success small pt-1 fw-bold"><?= round(($links_to_scrape[0]['url_to_scrape'] - 1) * 100 / $links_to_scrape[0]['total_link'], 2) . "%" ?></span> <span class="text-muted small pt-2 ps-1">dei link totali</span>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              
              <div class="col-xxl-8 col-md-6">
                <div class="card info-card customers-card text-center">

                  <div class="card-body">

                    <form class="row g-3">

                      <div class="col-md-12">
                      </div>

                      <div class="col-md-12">
                        <div class="form-floating">
                          <input type="number" class="form-control" id="max_urls_selector" placeholder="Your Name" min="1" max="100" value="1">
                          <label for="max_urls_selector">Numero di link da analizzare</label>
                        </div>
                      </div>

                      <div class="col-md-12">
                      </div>



                      <div class="d-flex justify-content-center">
                        <div id="crawler_status">
                          <button type="button" class="btn btn-primary" id="crawler">Avvia analisi</button>
                        </div>

                      </div>

                    </form>
                  </div>

                </div>
              </div>

             
              <div class="col-12">
                <div class="card">

                  <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-info-circle"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                      <li class="dropdown-header text-start">
                        <h6>Core Web Vitals Info</h6>
                      </li>

                      <li>
                        <p class="dropdown-item" href="#">
                          Punteggio: <br><br>
                          <b style="color:red;">0-49: Scarso</b> <br>
                          <b style="color:orange;">50-89: Ottimizzabile</b><br>
                          <b style="color:green;">90-100: Buono</b><br>
                        </p>
                      </li>
                      <li><a class="dropdown-item" href="https://web.dev/vitals/" target="_blank">Scopri di più <i class="bi bi-box-arrow-up-right"></i></a></li>
                    </ul>
                  </div>

                  <div class="card-body">
                    <h5 class="card-title">Core Web Vitals <span>> Punteggio > Generale</span></h5>

                    
                    <div id="columnChart"></div>

                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#columnChart"), {
                          series: [{
                            name: 'FID',
                            data: [<?= $mobile_fid_avg ?>, <?= $desktop_fid_avg ?>]
                          }, {
                            name: 'LCP',
                            data: [<?= $mobile_lcp_avg ?>, <?= $desktop_lcp_avg ?>]
                          }, {
                            name: 'CLS',
                            data: [<?= $mobile_cls_avg ?>, <?= $desktop_cls_avg ?>]
                          }, {
                            name: 'Speed Index',
                            data: [<?= $mobile_si_avg ?>, <?= $desktop_si_avg ?>]
                          }, {
                            name: 'Punteggio Generale',
                            data: [<?= $mobile_overall_avg ?>, <?= $desktop_overall_avg ?>]
                          }],
                          chart: {
                            type: 'bar',
                            height: 300
                          },
                          plotOptions: {
                            bar: {
                              horizontal: false,
                              columnWidth: '55%',
                              endingShape: 'rounded'
                            },
                          },
                          dataLabels: {
                            enabled: false
                          },
                          stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                          },
                          xaxis: {
                            categories: ['Mobile', 'Desktop'],
                          },
                          yaxis: {
                            title: {
                              text: 'Punteggio in %'
                            }
                          },
                          fill: {
                            opacity: 1
                          },
                          tooltip: {
                            y: {
                              formatter: function(val) {
                                return val + " %"
                              }
                            }
                          }
                        }).render();
                      });
                    </script>
                    

                  </div>

                </div>
              </div>



             
              <div class="col-6">
                <div class="card">


                  <div class="card-body pb-0">
                    <h5 class="card-title">URLs presenti nella sitemap <span>> Generale</span></h5>

                    <div id="sitemap_chart" style="min-height: 400px;" class="echart"></div>

                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        echarts.init(document.querySelector("#sitemap_chart")).setOption({
                          tooltip: {
                            trigger: 'item'
                          },
                          legend: {
                            top: '5%',
                            left: 'center'
                          },
                          series: [{
                            name: '',
                            type: 'pie',
                            radius: '70%',
                            avoidLabelOverlap: false,
                            label: {
                              show: false,
                              position: 'center'
                            },
                            emphasis: {
                              label: {
                                show: true,
                                fontSize: '18',
                                fontWeight: 'bold'
                              }
                            },
                            labelLine: {
                              show: false
                            },
                            data: [<?php

                                    echo "{name: 'URL presenti', value: {$url_in_sitemap['Sì']} },{name: 'URL non presenti', value: {$url_in_sitemap['No']}}";

                                    ?>]
                          }]
                        });
                      });
                    </script>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="card">


                  <div class="card-body pb-0">
                    <h5 class="card-title">Permessi del robots.txt <span>> Generale</span></h5>

                    <div id="robots_chart" style="min-height: 400px;" class="echart"></div>

                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        echarts.init(document.querySelector("#robots_chart")).setOption({
                          tooltip: {
                            trigger: 'item'
                          },
                          legend: {
                            top: '5%',
                            left: 'center'
                          },
                          series: [{
                            name: '',
                            type: 'pie',
                            radius: '70%',
                            avoidLabelOverlap: false,
                            label: {
                              show: false,
                              position: 'center'
                            },
                            emphasis: {
                              label: {
                                show: true,
                                fontSize: '18',
                                fontWeight: 'bold'
                              }
                            },
                            labelLine: {
                              show: false
                            },
                            data: [<?php

                                    echo "{name: 'Pagine bloccate', value: {$crawl_allowed['No']}},{name: 'Pagine non bloccate', value: {$crawl_allowed['Sì']}}";

                                    ?>]
                          }]
                        });
                      });
                    </script>
                  </div>
                </div>
              </div>

            </div>
          </div>

        
          <div class="col-lg-4">



            
            <div class="col-12">
              <div class="card">


                <div class="card-body pb-0">
                  <h5 class="card-title">HTTP Status <span>> Generale</span></h5>

                  <div id="http_status_chart" style="min-height: 400px;" class="echart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      echarts.init(document.querySelector("#http_status_chart")).setOption({
                        tooltip: {
                          trigger: 'item'
                        },
                        legend: {
                          top: '5%',
                          left: 'center'
                        },
                        series: [{
                          name: '',
                          type: 'pie',
                          radius: ['40%', '70%'],
                          avoidLabelOverlap: false,
                          label: {
                            show: false,
                            position: 'center'
                          },
                          emphasis: {
                            label: {
                              show: true,
                              fontSize: '18',
                              fontWeight: 'bold',
                            }
                          },
                          labelLine: {
                            show: false
                          },
                          data: [
                            <?php
                            foreach ($http_status as $status => $value) {
                              echo "{name: '{$status}', value: {$value}},";
                            }
                            ?>
                          ]
                        }]
                      });
                    });
                  </script>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="card">


                <div class="card-body pb-0">
                  <h5 class="card-title">Protocollo <span>> Generale</span></h5>

                  <div id="protocol_chart" style="min-height: 400px;" class="echart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      echarts.init(document.querySelector("#protocol_chart")).setOption({
                        tooltip: {
                          trigger: 'item'
                        },
                        legend: {
                          top: '5%',
                          left: 'center'
                        },
                        series: [{
                          name: '',
                          type: 'pie',
                          radius: ['40%', '70%'],
                          avoidLabelOverlap: false,
                          label: {
                            show: false,
                            position: 'center'
                          },
                          emphasis: {
                            label: {
                              show: true,
                              fontSize: '18',
                              fontWeight: 'bold'
                            }
                          },
                          labelLine: {
                            show: false
                          },
                          data: [
                            <?php
                            foreach ($protocol as $status => $value) {
                              echo "{name: '{$status}', value: {$value}},";
                            }
                            ?>
                          ]
                        }]
                      });
                    });
                  </script>
                </div>
              </div>
            </div>


          </div>

          <div class="col-lg-12">
            <div class="row">
              
              <div class="col-12">
                <div class="card recent-sales overflow-auto">



                  <div class="card-body">
                    <h5 class="card-title">URLs analizzati <span>> <?= $_GET['site'] ?></span></h5>

                    <table class="table table-hover table-borderless datatable" id="url_analizzati">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">URL</th>
                          <th scope="col">Data di analisi</th>
                          <th scope="col">Protocollo</th>
                          <th scope="col">HTTP status</th>
                          <th scope="col">Link Interni</th>
                          <th scope="col">Link esterni</th>
                          <th scope="col">Presente nella sitemap</th>
                          <th scope="col">Noindex</th>
                          <th scope="col">Nofollow</th>
                          <th scope="col">Permesso robots.txt</th>
                          <th scope="col">Titolo</th>
                          <th scope="col">Lingua</th>
                          <th scope="col">Link canonico</th>
                          <th scope="col">Alt tag in tutte le immagini</th>


                        </tr>
                      </thead>
                      <tbody>


                        <?php
                        if ($urls) {

                          foreach ($urls as $url) :
                        ?>
                            <tr class="item urls" onClick="show_charts(this)">
                              <?php
                              foreach ($url as $titolo => $valore) :
                              ?>
                                <td <?php if ($titolo == 'link_id') {
                                      echo ('id = "' . $valore . '"');
                                    } ?>><?php



                                      if (!isset($valore)) {
                                        echo "<i class='bx bxs-error-circle' ></i>";
                                      } else {
                                        if ($valore === false) {
                                          echo "<i class='bx bxs-circle' style='color:#ff0000' ></i>";
                                        } elseif ($valore === true) {
                                          echo "<i class='bx bxs-circle' style='color:#00ff00' ></i>";
                                        } else {
                                          echo $valore;
                                        }
                                      } ?></td>
                              <?php
                              endforeach;
                              ?>
                            </tr><?php
                                endforeach;
                              }
                                  ?>
                      </tbody>
                    </table>

                  </div>

                </div>
              </div>
            </div>
          </div> <!--FINE DELLA RIGA CENTRALE -->



          <div id="dettagli_url" class="col-lg-12 hide"> <!-- INIZIO SEZIONE DETTAGLI URL -->
            <div class="row">


              <div class="col-lg-8"> <!-- INIZIO COLONNA SINISTRA -->
                <div class="row">

               
                  <div class="col-sm-12 col-lg-12 ">
                    <div class="card">
                      <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-info-circle"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                          <li class="dropdown-header text-start">
                            <h6>Core Web Vitals Info</h6>
                          </li>
                          <li>
                            <p class="dropdown-item" href="#">
                              Punteggio: <br><br>
                              <b style="color:red;">0-49: Scarso</b> <br>
                              <b style="color:orange;">50-89: Ottimizzabile</b><br>
                              <b style="color:green;">90-100: Buono</b><br>
                            </p>
                          </li>
                          <li><a class="dropdown-item" href="https://web.dev/vitals/" target="_blank">Scopri di più <i class="bi bi-box-arrow-up-right"></i></a></li>
                        </ul>
                      </div>
                      <div class="card-body">
                        <h5 class="card-title">Core Web Vitals <span>> Punteggio > </span><span class="selected_url"></span></h5>
                        <div id="chartCWV"></div>
                      </div>
                    </div>
                  </div> <!-- FINE GRAFICO CORE WEB VITALS -->



                  <div class="col-lg-6"> <!-- INIZIO GRAFICO HTAGS -->
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">HTAGS <span>> </span><span class="selected_url"></span></h5>
                        <div id="htagsChart"></div>
                      </div>
                    </div>
                  </div> <!-- FINE GRAFICO HTAGS -->

                  <div class="col-lg-6">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Keywords <span>> </span><span class="selected_url"></span></h5>


                        <ol class="list-group list-group-numbered" id="keywords_list">
                        </ol>

                      </div>
                    </div>
                  </div>




                </div>
              </div> <!-- FINE COLONNA SINISTRA -->


              <div class="col-lg-4"> <!-- INIZIO COLONNA DESTRA -->
                <div class="row">


                  <div class="col-sm-12 col-lg-12 "> <!-- GRAFICO PERFORMANCE -->
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Core Web Vitals <span>> Performance > </span><span class="selected_url"></span></h5>
                        <ul class="nav nav-tabs d-flex" id="myTabjustified" role="tablist">
                          <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#chartFID" type="button" role="tab" aria-controls="home" aria-selected="true">FID</button>
                          </li>
                          <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="profile-tab" data-bs-toggle="tab" data-bs-target="#chartLCP" type="button" role="tab" aria-controls="profile" aria-selected="false">LCP</button>
                          </li>
                          <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="contact-tab" data-bs-toggle="tab" data-bs-target="#chartCLS" type="button" role="tab" aria-controls="contact" aria-selected="false">CLS</button>
                          </li>
                          <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="contact-tab" data-bs-toggle="tab" data-bs-target="#chartSI" type="button" role="tab" aria-controls="contact" aria-selected="false">Speed Index</button>
                          </li>
                        </ul>
                        <div class="tab-content pt-2" id="myTabjustifiedContent">
                          <div class="tab-pane fade show active " id="chartFID" role="tabpanel" aria-labelledby="home-tab">
                            <span>(Riferimento in millisecondi)</span>
                            <div id="chartFID_mobile"></div>
                            <div id="chartFID_desktop"></div>
                          </div>
                          <div class="tab-pane fade" id="chartLCP" role="tabpanel" aria-labelledby="profile-tab">
                            <span>(Riferimento in secondi)</span>
                            <div id="chartLCP_mobile"></div>
                            <div id="chartLCP_desktop"></div>
                          </div>
                          <div class="tab-pane fade" id="chartCLS" role="tabpanel" aria-labelledby="contact-tab">
                            <div id="chartCLS_mobile"></div>
                            <div id="chartCLS_desktop"></div>
                          </div>
                          <div class="tab-pane fade" id="chartSI" role="tabpanel" aria-labelledby="contact-tab">
                            <span>(Riferimento in secondi)</span>
                            <div id="chartSI_mobile"></div>
                            <div id="chartSI_desktop"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div> <!-- FINE GRAFICO PERFORMANCE -->


                </div>
              </div> <!-- FINE COLONNA DI DESTRA -->


            </div>
          </div> <!-- FINE SEZIONE DETTAGLI URL -->

        </div>
      </div>

    <?php endif; ?>
  </section>

</main>