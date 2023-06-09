#######################################     LIBRERIE    ####################################################
import os
from requests_html import HTMLSession
import json
import sys
from usp.tree import sitemap_tree_for_homepage
import requests
from urllib.parse import urlparse

#######################################     FUNZIONI    ####################################################


#FUNZIONE PER CERCARE LA SITEMAP NEL SITO WEB
def find_sitemap(nome_utente, url_sito, nome_sito):

    tree = sitemap_tree_for_homepage(url_sito)
   
    if (tree):
        link_id = 0
        sitemap_link = []
        links_to_scrape = [] 

        for page in tree.all_pages():
            url_site = page.url
                       
            link_id += 1

            data_site1 = {"url" : url_site}
            sitemap_link.append(data_site1)

            data_site2 = {"url" : url_site, "link_id" : link_id}
            links_to_scrape.append(data_site2)

        if len(links_to_scrape) == 0:
            link_id += 1
            self_url = {"url" : url_sito, "link_id" : link_id}
            links_to_scrape.append(self_url)
            status = "not found"
        else:
            status ="found"

        data = list(sitemap_link)
        json_string = json.dumps(data)
        json_string = json.loads(json_string)
        dir = 'risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/links_sitemap.json'
        with open(dir, 'w') as outfile:
            json.dump(json_string, outfile,indent=4)

        info = {"url_to_scrape" : 1, "total_link" : link_id}
        links_to_scrape = [info] + links_to_scrape
        data = list(links_to_scrape)

        json_string = json.dumps(data)
        json_string = json.loads(json_string)
        dir = 'risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/links_to_scrape.json'
        with open(dir, 'w') as outfile:
            json.dump(json_string, outfile,indent=4)

        json_string = json.dumps([])
        json_string = json.loads(json_string)
        dir = 'risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/scraped_links.json'
        with open(dir, 'w') as outfile:
            json.dump(json_string, outfile,indent=4)

        if status == "found":
            return 200
        else:
            return 404
            
    else:
        return 404




#######################################     SCRIPT    ####################################################


#prendo i dati passati in GET dall'url
url = sys.argv[1]
user = sys.argv[2]
site_to_scrape = sys.argv[3]


try:
    sitemap = find_sitemap(user, url, site_to_scrape)
    print(sitemap)
except:
    print("error")
