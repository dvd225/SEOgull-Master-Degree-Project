<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tool extends CI_Controller
{

	function load_page($content, $active_link_sidebar, $data_to_pass = null)
	{
		$id = $this->session->userdata('id');


		$data["user_sites"] = $this->crud_model->read_object(table: 'database_sites', where_table_name: 'user_id', where_table_value: $id, order_by: "fetch_date", asc: "DESC");
		$data['active_link_sidebar'] = $active_link_sidebar;

		$this->load->view('USER/header.php');
		$this->load->view('USER/sidebar.php', $data);
		$this->load->view("{$content}", $data_to_pass);
		$this->load->view('USER/footer.php');
	}

	public function index()
	{
		$id = $this->session->userdata('id');
		$data["recent_user_sites"] = $this->crud_model->read_object(table: 'database_sites', where_table_name: 'user_id', where_table_value: $id, order_by: "fetch_date", asc: "DESC", limit: 5);
		$active_link_sidebar = "analizza_nuovo_sito";

		$this->load_page('USER/homepage.php', $active_link_sidebar, $data);
	}

	public function check_folder_exists()
	{
		$url =  $this->input->get('url');
		$user = $this->input->get('user');
		$hostname = parse_url($url)['host'];
		$folder = "C:/xampp/htdocs/TESI/risorse/database_sites/$user/$hostname";
		echo is_dir($folder);
	}

	public function crea_cartella()
	{
		$url =  $this->input->get('url');
		$user =  $this->input->get('user');

		$script = "C:/Users/Dvdmo/AppData/Local/Programs/Python/Python39/python.exe C:/xampp/htdocs/TESI/risorse/py/crea_cartella.py " . $url . " " . $user;
		$result = exec($script);

		if ($result) {

			$id = $this->session->userdata('id');

			$site = $this->crud_model->read_object(table: 'database_sites', select: '*', where_table_name: ['site_name', 'user_id'], where_table_value: [$result, $id]);
			// var_dump($this->db->last_query());

			if (!$site) {
				$check_site = false;
			} else {
				$check_site = true;
			}


			if (!$check_site) {
				$path = "database_sites/$user/$result";

				$new_site = array(
					"user_id" => $id,
					"path" => $path,
					"site_name" => parse_url($url)['host'],
					"fetch_date" => date("Y/m/d")
				);
				$insert = $this->crud_model->create_object("database_sites", $new_site);
			} else {

				$id_site = $site[0]->id_site;

				$site_update = array(
					"fetch_date" => date("Y/m/d")
				);

				$update = $this->crud_model->update_object(table: "database_sites", where_table_name: 'id_site', where_table_value: $id_site, data: $site_update);
				var_dump($this->db->last_query());
			}
		}
	}

	public function trova_sitemap()
	{
		$url =  $this->input->get('url');
		$user =  $this->input->get('user');
		$site_name = parse_url($url)['host'];

		$script = "C:/Users/Dvdmo/AppData/Local/Programs/Python/Python39/python.exe C:/xampp/htdocs/TESI/risorse/py/cerca_sitemap.py " . $url . " " . $user . " " . $site_name;
		var_dump($script);
		$result = exec($script);
	}

	public function crawl()
	{
		$site_name =  $this->input->get('site_name');
		$user =  $this->input->get('user');
		$max_urls =  $this->input->get('max_urls');

		$script = "C:/Users/Dvdmo/AppData/Local/Programs/Python/Python39/python.exe C:/xampp/htdocs/TESI/risorse/py/analisi_crawler.py " . $site_name . " " . $user . " " . $max_urls;
		$result = system($script);
	}

	public function add_to_favorites()
	{
		$id =  $this->input->get('id');
		$favorite = $this->crud_model->read_object(table: 'database_sites', select: 'preferiti', where_table_name: 'id_site', where_table_value: $id);

		$favorite = $favorite[0]->preferiti;

		if ($favorite == 0) {
			$favorite = 1;
		} elseif ($favorite == 1) {
			$favorite = 0;
		}
		$update_favorite = array(
			"preferiti" => $favorite
		);
		$update = $this->crud_model->update_object(table: "database_sites", where_table_name: "id_site", where_table_value: $id, data: $update_favorite);
	}

	public function average($array)
	{
		if (!is_array($array)) return false;

		$sum = array_sum($array);
		$count = count($array);

		if ($sum != 0 && $count != 0) {
			try {
				$ris = round($sum / $count, 2);
			} catch (Exception $e) {
				$ris = 0;
			}
			return $ris;
		} else {
			return 0;
		}
	}

	public function dashboard()
	{
		if (null !== $this->input->get('site')) {
			$site =  $this->input->get('site');
			$user_id = $this->session->userdata('id');



			$data["dashboard_site"] = $this->crud_model->read_object(table: 'database_sites', select: '*', where_table_name: ['site_name', 'user_id'], where_table_value: [$site, $user_id]);

			$links_to_scrape = file_get_contents("{$this->config->item('risorse')['database_sites']}/{$this->session->userdata('username')}/{$data["dashboard_site"][0]->site_name}/links_to_scrape.json");
			$scraped_links = file_get_contents("{$this->config->item('risorse')['database_sites']}/{$this->session->userdata('username')}/{$data["dashboard_site"][0]->site_name}/scraped_links.json");
			$data["links_to_scrape"] = json_decode($links_to_scrape, true);

			$scraped_links = json_decode($scraped_links, true);

			$urls = [];

			$mobile_fid_list = [];
			$mobile_lcp_list = [];
			$mobile_cls_list = [];
			$mobile_si_list = [];
			$mobile_overall_list = [];

			$desktop_fid_list = [];
			$desktop_lcp_list = [];
			$desktop_cls_list = [];
			$desktop_si_list = [];
			$desktop_overall_list = [];

			$http_status = [];
			$protocol = [];
			$crawl_allowed = ["Sì" => 0, "No" => 0];
			$url_in_sitemap = ["Sì" => 0, "No" => 0];



			foreach ($scraped_links as $link) {
				$info = [];

				if ($link['status_code'] == "404") {
					$info = [
						"link_id" => $link['link_id'],
						"url" => $link['url'],
						"fetch_time" => $link['fetch_time'],
						"protocol" => null,
						"status_code" => $link['status_code'],
						"internal_links" => null,
						"external_links" => null,
						"located_in_sitemap" => null,
						"noindex" => null,
						"nofollow" => null,
						"crawl_allowed_by_robotx.txt" => null,
						"title" => null,
						"lenguage" => null,
						"canonical_link" => null,
						"alt_tag" => null,
					];
					$urls[] = $info;
				} else {

					foreach ($link as $name => $value) {
						if ($name == "core_web_vitals" or $name == "keywords" or  $name == "htag") {
							continue;
						}
						$info[$name] = $value;
					};
					$urls[] = $info;
				}





				if (array_key_exists($link['status_code'], $http_status)) {
					$http_status[$link['status_code']]++;
				} else {
					$http_status[$link['status_code']] = 1;
				}

				if (isset($link['core_web_vitals'])) {
					$mobile_fid_list[] = $link['core_web_vitals'][0]['mobile']['first_input_delay_score'];
					$mobile_lcp_list[] = $link['core_web_vitals'][0]['mobile']['largest_contentuful_paint_score'];
					$mobile_cls_list[] = $link['core_web_vitals'][0]['mobile']['cumulative_layout_shift_score'];
					$mobile_si_list[] = $link['core_web_vitals'][0]['mobile']['speed_index_score'];
					$mobile_overall_list[] = $link['core_web_vitals'][0]['mobile']['overall_score'];

					$desktop_fid_list[] = $link['core_web_vitals'][0]['desktop']['first_input_delay_score'];
					$desktop_lcp_list[] = $link['core_web_vitals'][0]['desktop']['largest_contentuful_paint_score'];
					$desktop_cls_list[] = $link['core_web_vitals'][0]['desktop']['cumulative_layout_shift_score'];
					$desktop_si_list[] = $link['core_web_vitals'][0]['desktop']['speed_index_score'];
					$desktop_overall_list[] = $link['core_web_vitals'][0]['desktop']['overall_score'];

					if (array_key_exists($link['protocol'], $protocol)) {
						$protocol[$link['protocol']]++;
					} else {
						$protocol[$link['protocol']] = 1;
					}

					if (!$link['located_in_sitemap']) {
						$url_in_sitemap['No']++;
					} else {
						$url_in_sitemap['Sì']++;
					}


					if ($link['crawl_allowed_by_robotx.txt']) {
						$crawl_allowed['Sì']++;
					} else {
						$crawl_allowed['No']++;
					}
				} else {
					continue;
				}
			}

			$data['urls'] = $urls;

			$data['http_status'] = $http_status;
			$data['protocol'] = $protocol;
			$data['crawl_allowed'] = $crawl_allowed;
			$data['url_in_sitemap'] = $url_in_sitemap;

			$data["mobile_fid_avg"] = $this->average($mobile_fid_list);
			$data["mobile_lcp_avg"] = $this->average($mobile_lcp_list);
			$data["mobile_cls_avg"] = $this->average($mobile_cls_list);
			$data["mobile_si_avg"] = $this->average($mobile_si_list);
			$data["mobile_overall_avg"] = $this->average($mobile_overall_list);

			$data["desktop_fid_avg"] = $this->average($desktop_fid_list);
			$data["desktop_lcp_avg"] = $this->average($desktop_lcp_list);
			$data["desktop_cls_avg"] = $this->average($desktop_cls_list);
			$data["desktop_si_avg"] = $this->average($desktop_si_list);
			$data["desktop_overall_avg"] = $this->average($desktop_overall_list);

			$active_link_sidebar = "dashboard";

			$this->load_page('USER/dashboard.php', $active_link_sidebar, $data);
		} else {
			$active_link_sidebar = "dashboard";
			$this->load_page('USER/dashboard.php', $active_link_sidebar);
		}
	}

	public function show_charts()
	{

		$site = trim($this->input->get('site'), " ");
		$url_id =  $this->input->get('url_id') - 1;
		$results = [];

		$scraped_links = file_get_contents("{$this->config->item('risorse')['database_sites']}/{$this->session->userdata('username')}/{$site}/scraped_links.json");
		$json = json_decode($scraped_links, true);

		$results[] = $json[$url_id]['core_web_vitals'][0];
		$results[] = $json[$url_id]['htag'];
		$results[] = $json[$url_id]['keywords'];

		echo json_encode($results);
	}

	public function profile()
	{
		$user = $this->crud_model->read_object(table: 'users', select: '*', where_table_name: 'id', where_table_value: $this->session->userdata('id'));
		$data['user_info'] = $user[0];
		$active_link_sidebar =  "profilo";

		$this->load_page('USER/profile.php', $active_link_sidebar, $data);
	}

	public function update_profile()
	{
		$name =  $this->input->get('name');
		$azienda =  $this->input->get('azienda');

		$user_update = array(
			"fullname" => $name,
			"azienda" => $azienda
		);

		$update = $this->crud_model->update_object(table: "users", where_table_name: 'id', where_table_value: $this->session->userdata('id'), data: $user_update);
	}

	public function update_password()
	{
		$old =  $this->input->get('old');
		$new =  $this->input->get('new');

		$user = $this->crud_model->read_object(table: 'users', select: '*', where_table_name: 'id', where_table_value: $this->session->userdata('id'));

		if (password_verify($old, $user[0]->password)) {
			$pw_update = array(
				"password" => password_hash($new, PASSWORD_DEFAULT)
			);

			$update = $this->crud_model->update_object(table: "users", where_table_name: 'id', where_table_value: $this->session->userdata('id'), data: $pw_update);
			if ($update) {
				echo "true";
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
