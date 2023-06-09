#######################################     LIBRERIE    ####################################################
import os
from requests_html import HTMLSession
import json
import sys
from usp.tree import sitemap_tree_for_homepage
import requests
from urllib.parse import urlparse




#######################################     FUNZIONI    ####################################################

#FUNZIONE PER CREARE I FILE NELLA DIRECTORY
def save_file_at_dir(dir_path, filename, file_content, mode='w'):
    os.makedirs(dir_path, exist_ok=True)
    with open(os.path.join(dir_path, filename), mode) as f:
        f.write(file_content)
    return True

#FUNZIONE PER AGGIUNGERE NEL DATABASE LA DIRECTORY PER IL SITO ANALIZZATO
def add_site_dir(nome_utente, nome_sito):
    sitemap = save_file_at_dir('risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/' , 'links_sitemap.json', '')
    links_to_scrape = save_file_at_dir('risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/' , 'links_to_scrape.json', '')
    scraped_links = save_file_at_dir('risorse/database_sites/' + nome_utente + '/'+ nome_sito + '/' , 'scraped_links.json', '')

    if sitemap and links_to_scrape and scraped_links:
        return 1
    else:
        return 0


#######################################     SCRIPT    ####################################################



#prendo i dati passati in GET dall'url
url = sys.argv[1]
user = sys.argv[2]

#estreggo il nome del sito dall'intero url
parsed_url = urlparse(url)
site_to_scrape = parsed_url.netloc

#creo la directory
directory = add_site_dir(user, site_to_scrape)
if (directory == 1):
    print (site_to_scrape)

