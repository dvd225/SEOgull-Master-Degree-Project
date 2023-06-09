function analizza_nuovo_sito(url, user) {

  document.getElementById('new_website_state').innerHTML = ''
  // rendo visibile il loading spinner
  document.getElementById('new_website_state').innerHTML += '<button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analisi in corso...</button>'

  // PRIMA CHIAMATA AJAX --> CREA DIRECTORY
  $.ajax({
    type: "GET",
    url: encodeURI("Tool/crea_cartella?url=" + url + "&user=" + user),
    success: function (result_ajax1) {
      console.log("ok")

      // SECONDA CHIAMATA AJAX --> CERCA SITEMAP
      $.ajax({
        type: "GET",
        url: encodeURI("Tool/trova_sitemap?url=" + url + "&user=" + user),

        success: function (result_ajax2) {

          // if (dati_ajax2 == 200) {
          //     sitemap_results = "Sitemap trovata";
          // } else if (dati_ajax2 == 404) {
          //     sitemap_results = "Sitemap non trovata";
          // } else if (dati_ajax2 == "error") {
          //     sitemap_results = "Errore, riprova";
          // }
          // document.getElementById('box').innerHTML += "<div>" + sitemap_results + "</div>"
          // $("#loading").css('visibility', 'hidden');
          document.getElementById('new_website_state').innerHTML = '<button type="submit" class="btn btn-primary" id="new_website" onClick="ajax_call()">Analizza</button>'
          document.getElementById('url_to_scrape').value = ''
          console.log("ok2")
          location.reload()
        },
        error: function (xhr, status, error) {
          alert(xhr.responseText);
        }
      })




    },
    error: function (xhr, status, error) {
      alert(xhr.responseText, error);
    }
  })
}

function confirm_override(url, user) {
  if (confirm("Attenzione! Una cartella per questo sito web è già presente nel database, vuoi sovrascriverla?")) {
    analizza_nuovo_sito(url, user)
  } else {
    document.getElementById('new_website_state').innerHTML = '<button type="submit" class="btn btn-primary" id="new_website" onClick="ajax_call()">Analizza</button>'
  }
}


function ajax_call() {

  var url = $("#url_to_scrape").val();
  var user = $("#logged_username").text();

  if (!url) {
    // location.reload()
  } else {
    $.ajax({
      type: "GET",
      url: encodeURI("Tool/check_folder_exists?url=" + url + "&user=" + user),

      success: function (folder_exists) {
        // console.log("php_script/cerca_sitemap.php?url=" + url + "&user=" + user + "&site_name=" + nome_sito)
        if (folder_exists) {
          confirm_override(url, user)
        } else {
          analizza_nuovo_sito(url, user)
        }
      },
      error: function (xhr, status, error) {
        alert(xhr.responseText);
      }
    })
  }
}


$("#new_website").click(function () {
  ajax_call()
})

const favorites_homepage = document.querySelectorAll('.fav-homepage');

favorites_homepage.forEach(favorites_homepage => {
  favorites_homepage.addEventListener('click', add_to_favotites_homepage);
});

function add_to_favotites_homepage(e) {
  var id = e.target.id;

  $.ajax({
    type: "GET",
    url: encodeURI("Tool/add_to_favorites?id=" + id),

    success: function () {
      location.reload()
    },
    error: function (xhr, status, error) {
      alert(xhr.responseText);
    }
  })
}

const favorites_dashboard = document.querySelectorAll('.fav-dashboard');

favorites_dashboard.forEach(favorites_dashboard => {
  favorites_dashboard.addEventListener('click', add_to_favotites_dashboard);
});

function add_to_favotites_dashboard(e) {
  var id = e.target.id;

  $.ajax({
    type: "GET",
    url: encodeURI("../Tool/add_to_favorites?id=" + id),

    success: function () {
      location.reload()
    },
    error: function (xhr, status, error) {
      alert(xhr.responseText);
    }
  })
}


function crawl() {
  var user = $("#logged_username").text()
  var site_name = $("#active_site_name").text()
  var max_urls = document.getElementById('max_urls_selector').value;

  console.log(max_urls)

  document.getElementById('crawler_status').innerHTML = ''
  // rendo visibile il loading spinner
  document.getElementById('crawler_status').innerHTML += '<button type="button" disabled class="btn btn-primary" id="crawler"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analisi in corso...</button>'

  $.ajax({
    type: "GET",
    url: encodeURI("../Tool/crawl?site_name=" + site_name + "&user=" + user + "&max_urls=" + max_urls),

    success: function (result_ajax2) {

      // document.getElementById('new_website_state').innerHTML = '<button type="submit" class="btn btn-primary" id="new_website" onClick="ajax_call()">Analizza</button>'
      // document.getElementById('url_to_scrape').value = ''
      // location.reload()
      document.getElementById('crawler_status').innerHTML = ''
      // rendo visibile il loading spinner
      document.getElementById('crawler_status').innerHTML += '<button type="button" class="btn btn-primary" id="crawler">Avvia analisi</button>'

      document.getElementById('notifica_alert').innerHTML = '!'
      document.getElementById('notifica_content').innerHTML = ''
      document.getElementById('notifica_content').innerHTML +=
        `<li class="dropdown-header">
    <p><i class="bi bi-check-circle text-success"></i> Analisi terminata. Aggiorna la pagina per vedere i risultati</p>
</li>
`;




    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    }
  })
}

$("#crawler_status").click(function () {
  crawl()
})

// const urls = document.querySelectorAll('.urls');

// urls.forEach(urls => {
//   urls.addEventListener('click', show_charts);
// });

function show_charts(e) {
  // rows = $(this).parent().parent();
  // rows.removeClass( "table-warning" );
  $("#url_analizzati").parent().find('tr').removeClass("table-warning");
  $("#dettagli_url").css("display", "block");
  element = $(e);
  element.addClass("table-warning");


  var site = $("#active_site_name").text();
  var url_id = element.first()[0]['firstChild']['data']
  var url_name = element.first()[0]['firstChild']['nextElementSibling']['data']

  $.ajax({
    type: "GET",
    url: encodeURI("../Tool/show_charts?site=" + site + "&url_id=" + url_id),

    success: function (results) {
      results = JSON.parse(results)

      // CORE WEB VITALS SCORE CHART
      var items = document.getElementsByClassName("selected_url"),
        i, len;

      // loop through all elements having class name ".my-class"
      for (i = 0, len = items.length; i < len; i++) {
        items[i].innerHTML = url_name;
      }
      document.getElementById('chartCWV').innerHTML = ''

      var chart = new ApexCharts(document.querySelector("#chartCWV"), {
        series: [{
          name: 'FID',
          data: [results[0]['mobile']['first_input_delay_score'], results[0]['desktop']['first_input_delay_score']]
        }, {
          name: 'LCP',
          data: [results[0]['mobile']['largest_contentuful_paint_score'], results[0]['desktop']['largest_contentuful_paint_score']]
        }, {
          name: 'CLS',
          data: [results[0]['mobile']['cumulative_layout_shift_score'], results[0]['desktop']['cumulative_layout_shift_score']]
        }, {
          name: 'Speed Index',
          data: [results[0]['mobile']['speed_index_score'], results[0]['desktop']['speed_index_score']]
        }, {
          name: 'Punteggio Generale',
          data: [results[0]['mobile']['overall_score'], results[0]['desktop']['overall_score']]
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
            formatter: function (val) {
              return val + " %"
            }
          }
        }
      }).render();

      // CORE WEB VITALS RELATIVE CHARTS

      //FID MOBILE
      var fid_mobile = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['mobile']['first_input_delay'],
          title: { text: "Mobile" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 500] },
            bar: { color: "black" },
            steps: [
              { range: [0, 100], color: "green" },
              { range: [100, 300], color: "orange" },
              { range: [300, 500], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 490
            }
          }
        }
      ];

      //FID DESKTOP
      var fid_desktop = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['desktop']['first_input_delay'],
          title: { text: "Desktop" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 500] },
            bar: { color: "black" },
            steps: [
              { range: [0, 100], color: "green" },
              { range: [100, 300], color: "orange" },
              { range: [300, 500], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 490
            }
          }
        }
      ];

      //LCP MOBILE
      var lcp_mobile = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['mobile']['largest_contentuful_paint'] / 1000,
          title: { text: "Mobile" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 10] },
            bar: { color: "black" },
            steps: [
              { range: [0, 2.5], color: "green" },
              { range: [2.5, 4], color: "orange" },
              { range: [4, 10], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 9.8
            }
          }
        }
      ];

      //LCP DESKTOP
      var lcp_desktop = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['desktop']['largest_contentuful_paint'] / 1000,
          title: { text: "Desktop" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 10] },
            bar: { color: "black" },
            steps: [
              { range: [0, 2.5], color: "green" },
              { range: [2.5, 4], color: "orange" },
              { range: [4, 10], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 9.8
            }
          }
        }
      ];

      //CLS MOBILE
      var cls_mobile = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['mobile']['cumulative_layout_shift'],
          title: { text: "Mobile" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 0.5] },
            bar: { color: "black" },
            steps: [
              { range: [0, 0.1], color: "green" },
              { range: [0.1, 0.25], color: "orange" },
              { range: [0.25, 0.5], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 0.49
            }
          }
        }
      ];

      //CLS DESKTOP
      var cls_desktop = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['desktop']['cumulative_layout_shift'],
          title: { text: "Desktop" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 0.5] },
            bar: { color: "black" },
            steps: [
              { range: [0, 0.1], color: "green" },
              { range: [0.1, 0.25], color: "orange" },
              { range: [0.25, 0.5], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 0.49
            }
          }
        }
      ];

      //SPEED INDEX MOBILE
      var speed_index_mobile = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['mobile']['speed_index'] / 1000,
          title: { text: "Mobile" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 10] },
            bar: { color: "black" },
            steps: [
              { range: [0, 3.4], color: "green" },
              { range: [3.4, 5.8], color: "orange" },
              { range: [5.8, 10], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 9.8
            }
          }
        }
      ];

      //SPEED INDEX DESKTOP
      var speed_index_desktop = [
        {
          domain: { x: [0, 1], y: [0, 1] },
          value: results[0]['desktop']['speed_index'] / 1000,
          title: { text: "Desktop" },
          type: "indicator",
          mode: "gauge+number",
          gauge: {
            axis: { range: [null, 10] },
            bar: { color: "black" },
            steps: [
              { range: [0, 3.4], color: "green" },
              { range: [3.4, 5.8], color: "orange" },
              { range: [5.8, 10], color: "red" }

            ],
            threshold: {
              line: { color: "black", width: 4 },
              thickness: 0.75,
              value: 9.8
            }
          }
        }
      ];




      var config = { responsive: true }
      var chartFID_mobile = document.getElementById('chartFID_mobile')
      var chartFID_desktop = document.getElementById('chartFID_desktop')

      var chartLCP_mobile = document.getElementById('chartLCP_mobile')
      var chartLCP_desktop = document.getElementById('chartLCP_desktop')

      var chartCLS_mobile = document.getElementById('chartCLS_mobile')
      var chartCLS_desktop = document.getElementById('chartCLS_desktop')

      var chartSI_mobile = document.getElementById('chartSI_mobile')
      var chartSI_desktop = document.getElementById('chartSI_desktop')

      var layout = { width: 400, height: 250, margin: { t: 0, b: 0 } };

      Plotly.newPlot(chartFID_mobile, fid_mobile, layout, config);
      Plotly.newPlot(chartFID_desktop, fid_desktop, layout, config);

      Plotly.newPlot(chartLCP_mobile, lcp_mobile, layout, config);
      Plotly.newPlot(chartLCP_desktop, lcp_desktop, layout, config);

      Plotly.newPlot(chartCLS_mobile, cls_mobile, layout, config);
      Plotly.newPlot(chartCLS_desktop, cls_desktop, layout, config);

      Plotly.newPlot(chartSI_mobile, speed_index_mobile, layout, config);
      Plotly.newPlot(chartSI_desktop, speed_index_desktop, layout, config);

      var htags = []
      for (const [key, value] of Object.entries(results[1])) {
        // console.log(key, value);
        htags.push(value)
      }

      document.getElementById('htagsChart').innerHTML = ''

      var x = new ApexCharts(document.querySelector("#htagsChart"), {
        series: [{
          data: htags,
        }],
        chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: false,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        }
      }).render();


      var keywords = []
      for (const [key, value] of Object.entries(results[2])) {
        keywords.push(key)
      }

      document.getElementById('keywords_list').innerHTML = ''

      for (i = 0; i < keywords.length; i++) {
        document.getElementById('keywords_list').innerHTML += '<li class="list-group-item">' + keywords[i] + '</li>'
      }

      console.log(htags)
    },
    error: function (xhr, status, error) {
      alert(xhr.responseText);
    }
  })
}

$("#update_profile").click(function () {

  var new_fullname = $("#full_name").val();
  var new_azienda = $("#azienda").val();

  // console.log (new_azienda, new_fullname)

  $.ajax({
    type: "GET",
    url: encodeURI("../Tool/update_profile?name=" + new_fullname + "&azienda=" + new_azienda),

    success: function (result) {
      document.getElementById('button_result_profile').innerHTML = ''
      document.getElementById('button_result_profile').innerHTML = '<button type="button" class="btn btn-success">Modifiche salvate</button>'

    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    }
  })

})

$("#update_password").click(function () {

  var old_pw = $("#old_pw").val();
  var new_pw = $("#new_pw").val();
  var redo_new_pw = $("#redo_new_pw").val();

  if (redo_new_pw === new_pw) {
    $.ajax({
      type: "GET",
      url: encodeURI("../Tool/update_password?old=" + old_pw + "&new=" + new_pw),

      success: function (result) {
        if (result) {
          document.getElementById('button_result_pw').innerHTML = ''
          document.getElementById('button_result_pw').innerHTML = '<button type="button" class="btn btn-success">Modifiche salvate</button>'
        } else {
          document.getElementById('button_result_pw').innerHTML = ''
          document.getElementById('button_result_pw').innerHTML = '<button type="button" class="btn btn-danger">Qualcosa è andato storto</button>'

        }
      },
      error: function (xhr, status, error) {
        document.getElementById('button_result_pw').innerHTML = ''
        document.getElementById('button_result_pw').innerHTML = '<button type="button" class="btn btn-danger">Qualcosa è andato storto</button>'

      }
    })
  } else {
    document.getElementById('button_result_pw').innerHTML = ''
    document.getElementById('button_result_pw').innerHTML = '<button type="button" class="btn btn-warning">Le nuove password non corrispondono</button>'

  }

})

$("#all_sites").click(function () {
  $("#dettagli_site").css("display", "block");

})
