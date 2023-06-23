#######################################     LIBRERIE    ####################################################
import requests
from urllib.parse import urlparse, urljoin
from bs4 import BeautifulSoup
import sys
import json
from requests_html import HTMLSession
from protego import Protego
import pandas as pd
import urllib
from nltk import tokenize
from operator import itemgetter
import math
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize 
import re
import string
import traceback
from datetime import datetime


#######################################     FUNZIONI    ####################################################

def is_valid(url):

    parsed = urlparse(url)
    return bool(parsed.netloc) and bool(parsed.scheme)

def render(url, time):
    session = HTMLSession()
    r = session.get(url)
    r.html.render(timeout=time, sleep=5)
    return r

def get_all_website_links(r):

    urls = set()

    datas = r.html.absolute_links
    links = list(datas)

    for link in links:

        if link == "" or link is None:
            # SE IL LINK E' VUOTO
            continue

        if not is_valid(link):
            # SE IL LINK NON E' VALIDO
            continue

        urls.add(link)

    return urls


def update_links_to_scrape_info(directory, *total_link_and_last_id):

    o_file = open(directory, "r+")
    json_list = json.load(o_file)
    
    if total_link_and_last_id:                      # SE TOTAL_LINK E LAST_ID VENGONO PASSATI VUOL DIRE CHE L'URL FUNZIONA
        json_list[0]["total_link"] = total_link_and_last_id[0]
        json_list[0]["url_to_scrape"] = total_link_and_last_id[1] + 1
    else:                                           # ALTRIMENTI PROBABILMENTE L'URL E' UN 404, PERCIO' INCREMENTO IL CONTATORE DEI LINK DA ANALIZZARE
        json_list[0]["url_to_scrape"] = json_list[0]["url_to_scrape"] + 1

    file = open(directory, "w")
    json.dump(json_list, file, indent=4)


def update_links_to_scrape_list(new_data, directory):
    with open(directory, 'r+') as file:
       
        file_data = json.load(file)
        file_data.append(new_data)
        file.seek(0)
        json.dump(file_data, file, indent=4)


def update_scraped_links(new_data, directory):
    with open(directory, 'r+') as file:
       
        file_data = json.load(file)
        file_data.append(new_data)
        file.seek(0)
        json.dump(file_data, file, indent=4)


def query(url, key, strategy="desktop"):

    endpoint = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed"\
        + "?strategy="+strategy\
        + "&url={}"\
        + "&key="+key

    response = urllib.request.urlopen(
        endpoint.format(url)).read().decode('UTF-8')
    data = json.loads(response)

    return data


def get_core_web_vitals(report):

    fetch_time = report['lighthouseResult']['fetchTime']
    overall_score = report["lighthouseResult"]["categories"]["performance"]["score"] * 100

    speed_index =  report["lighthouseResult"]["audits"]["speed-index"]["numericValue"]
    speed_index_score = report["lighthouseResult"]["audits"]["speed-index"]["score"] * 100

    cumulative_layout_shift = report["lighthouseResult"]["audits"]["cumulative-layout-shift"]["numericValue"]
    cumulative_layout_shift_score = report["lighthouseResult"]["audits"]["cumulative-layout-shift"]["score"] * 100

    first_input_delay = report["lighthouseResult"]["audits"]["max-potential-fid"]["numericValue"]
    first_input_delay_score = report["lighthouseResult"]["audits"]["max-potential-fid"]["score"] * 100

    largest_contentful_paint = report["lighthouseResult"]["audits"]["largest-contentful-paint"]["numericValue"]
    largest_contentful_paint_score = report["lighthouseResult"]["audits"]["largest-contentful-paint"]["score"] * 100

    data = {
        'fetch_time': fetch_time,
        'overall_score': overall_score,
        'speed_index': speed_index,
        'speed_index_score' : speed_index_score,
        'LCP' : largest_contentful_paint,
        'LCP_score' : largest_contentful_paint_score,
        'FID' : first_input_delay,
        'FID_score' : first_input_delay_score,
        'CLS' : cumulative_layout_shift,
        'CLS_score' : cumulative_layout_shift_score
    }

    return data

def check_sent(word, sentences): 
    final = [all([w in x for w in word]) for x in sentences] 
    sent_len = [sentences[i] for i in range(0, len(final)) if final[i]]
    return int(len(sent_len))

def get_top_n(dict_elem, n):
            result = dict(sorted(dict_elem.items(), key = itemgetter(1), reverse = True)[:n]) 
            return result

def find_keywords(url, lang):
    source = requests.get(url).text
    text = BeautifulSoup(source, 'lxml').text
    
    # RIMUOVO CARATTERI NON ALFANUMERICI
    doc = re.sub(r"/W+", '', text)

    # DEFINISCO IL LINGUAGGIO DEL DIZIONARIO DI STOPWORDS DA USARE
    if not lang:
        stopwords_dictionary = "english" # CONDIZIONE DI DEFAULT
    elif "en" in lang:
        stopwords_dictionary = "english"
    elif "it" in lang:
        stopwords_dictionary = "italian"

    # RIMUOVO LE STOPWORDS
    stop_words = set(stopwords.words(stopwords_dictionary)) 
    # Step 1 : Trovo il numero totale di parole
    total_words = doc.split()
    total_word_length = len(total_words)

    # Step 2 : Trovo il numero totale di frasi
    total_sentences = tokenize.sent_tokenize(doc)
    total_sent_len = len(total_sentences)

    # Step 3: Calcolo TF per ogni parola
    tf_score = {}
    for each_word in total_words:
        each_word = each_word.replace('.','')
        if each_word not in stop_words:
            if each_word in tf_score:                tf_score[each_word] += 1
            else:
                tf_score[each_word] = 1

    tf_score.update((x, y/int(total_word_length)) for x, y in tf_score.items())

    # Step 4: Calcolo IDF per ogni parola
    idf_score = {}
    for each_word in total_words:
        each_word = each_word.translate(str.maketrans('', '', string.punctuation))

        if each_word not in stop_words:
            if each_word in idf_score:
                idf_score[each_word] = check_sent(each_word, total_sentences)
            else:
                idf_score[each_word] = 1

    idf_score.update((x, math.log(int(total_sent_len)/y)) for x, y in idf_score.items())

    # Step 5: Calcolo TF*IDF
    tf_idf_score = {key: tf_score[key] * idf_score.get(key, 0) for key in tf_score.keys()} 
    

    keywords = get_top_n(tf_idf_score, 10)
    return keywords


def crawl(max_urls, user, site_name):
    now = datetime.now()
    today = now.strftime("%d/%m/%Y")

    directory_links_sitemap = 'C:/xampp/htdocs/TESI/risorse/database_sites/' + \
        user + '/' + site_name + '/links_sitemap.json'
    directory_links_to_scrape = 'C:/xampp/htdocs/TESI/risorse/database_sites/' + \
        user + '/' + site_name + '/links_to_scrape.json'
    directory_scraped_links = 'C:/xampp/htdocs/TESI/risorse/database_sites/' + \
        user + '/' + site_name + '/scraped_links.json'

    # METTO OGNI LINK DEL FILE links_sitemap IN UNA LISTA, IN MODO DA POTER ESEGUIRE CONTROLLI
    with open(directory_links_sitemap) as f:
        sitemap = json.load(f)
    links_sitemap_list = []
    for link in sitemap:
        if 'url' in link.keys():
            links_sitemap_list.append(link["url"])

    for i in range(max_urls):
        try:
            internal_urls = set()
            external_urls = set()

            # APRO IL FILE LINKS_TO_SCRAPE
            with open(directory_links_to_scrape) as f:
                data = json.load(f)

            # PRENDO L'ID DELL'ULTIMO LINK ANALIZZATO
            last_id = data[0]['url_to_scrape']

            # PRENDO IL NUMERO TOTALE DEI LINK ANALIZZATI, CHE USERO' PER DEFINIRE IL NUOVO ID DELL'ELEMENTO DA AGGIUNGERE
            total_link = data[0]['total_link']

            # CREO DELLE LISTE VUOTE CHE RIEMPIRO'
            links_to_scrape_list = []
            links_to_add = []

            # METTO OGNI LINK DEL FILE links_to_scrape IN UNA LISTA, IN MODO DA POTER VERIFICARE SE UN LINK CHE VOGLIO AGGIUNGERE
            # E' GIA' PRESENTE O MENO
            for link in data:
                if 'url' in link.keys():
                    links_to_scrape_list.append(link["url"])

            # PRENDO L'URL DEL NUOVO LINK DA NAVIGARE
            try:   
                url = data[last_id]['url']    
            except:
                continue
            
            domain_name = urlparse(url).netloc

            # ANALISI:
            timeout=180
            r = render(url, timeout) # RENDERIZZO LA PAGINA CON UNA CHIAMATA REQUEST

            

            # 1) RACCOLGO DEI DATI SEMATICI

            # TITLE
            title = r.html.find('title', first=True).text

            # ALT TAGS
            img = list(r.html.find('img'))
            alt_tag = []

            for i in img:
                if 'alt' in i.attrs:
                    if (i.attrs['alt']):
                        alt_tag.append(True)
                    else:
                        alt_tag.append(False)

            alt_tag_check = all(item is True for item in alt_tag) # SE NELLA LISTA SONO PRESENTI SOLO TRUE SIGNIFICA CHE OGNI IMMAGINE AVEVA UN TAG ALT

            # CANONICAL TAG
            canonical = r.html.find('link')
            canonical_check = False

            if canonical:
                for c in canonical:
                    if 'rel' in c.attrs:
                        if "canonical" in c.attrs['rel']:
                            canonical_check = c.attrs['href']

            # H TAGS
            h1tags = r.html.find('h1')
            h2tags = r.html.find('h2')
            h3tags = r.html.find('h3')
            h4tags = r.html.find('h4')
            h5tags = r.html.find('h5')
            h6tags = r.html.find('h6')

            htags = {
                'h1' : len(h1tags),
                'h2' : len(h2tags),
                'h3' : len(h3tags),
                'h4' : len(h4tags),
                'h5' : len(h5tags),
                'h6' : len(h6tags)
            }

            # HTML LANG
            if 'lang' in r.html.find('html', first=True).attrs:
                lang = r.html.find('html', first=True).attrs['lang']
            else:
                lang = False

            # 2) PRENDO TUTTI I LINK
            
            links = get_all_website_links(r)

            # 3) VERIFICO HTTP STATUS
            request = requests.head(url)
            http_status = request.status_code

            # 4) VERIFICO SE IL LINK ERA GIA' PRESENTE NELLA SITEMAP
            link_in_sitemap = 0

            if url in links_sitemap_list:
                link_in_sitemap = True
            else:
                link_in_sitemap = False

            # 5) VERIFICO SE IL LINK POSSIEDE UN GENERICO ROBOTS META TAG NOINDEX E NOFOLLOW
            meta_tags = r.html.find('meta')
            meta_tags = list(meta_tags)
            noindex = False
            nofollow = False

            for meta in meta_tags:
                if 'name' in meta.attrs:
                    name = meta.attrs['name']
                    if "robots" in name:
                        content = meta.attrs['content']
                        if "nofollow" in content:
                            nofollow = True
                        if "noindex" in content:
                            nofollow = True
                        if "none" in content:  # UN VALUE "none" E' UGUALE A "nodindex, nofollow"
                            nofollow = True
                            nofollow = True

            # 6) VERIFICO CHE IL LINK NON SIA BLOCCATO DALLE CONDIZIONI DEL FILE ROBOTS.TXT

            splitted_link = url.split(domain_name)  # SPLITTO IL LINK
            robots = "/robots.txt"
            # PROTOCOLLO + DOMAIN NAME + /ROBOTS.TXT
            robotstxt_url = splitted_link[0] + domain_name + robots

            # ESEGUO UNA REQUEST DEL FILE ROBOTS.TXT
            response = requests.get(robotstxt_url)
            robotstxt_text = response.text          # LO CONVERTO IN STRINGA

            rp = Protego.parse(robotstxt_text)
            bot_name = "*"  # IL SIMBOLO * INDICA QUALSIASI BOT

            # SE IL ROBOTS.TXT NON BLOCCA IL LINK RESTITUISCE TRUE, SE BLOCCATO FALSE
            robotstxt_allow = rp.can_fetch(url, bot_name)

            # 7) RACCOLGO I DATI DEI CORE WEB VITALS

            key = "yoursecretgooglekey"  # SECRET KEY
            

            strategy = "mobile"
            report_mobile = query(url, key, strategy)
            core_web_vitals_mobile = get_core_web_vitals(report_mobile)

            strategy = "desktop"
            report_desktop = query(url, key, strategy)
            core_web_vitals_desktop = get_core_web_vitals(report_desktop)

            # 8) ANALIZZO LE KEYWORD DELLA PAGINA WEB

            keywords = find_keywords(url, lang)



            # PER OGNI LINK TROVATO
            for link in links:

                # AGGIUNGO IL LINK IN UNA LISTA LINK ESTERNO/INTERNO, SE E' ESTERNO PROSEGUO COL PROSSIMO LINK, SE E' INTERNO
                # PROSEGUO CON IL CICLO
                if link in internal_urls:
                    continue
                if (domain_name != urlparse(link).netloc):
                    if link not in external_urls:
                        external_urls.add(link)
                    continue
                internal_urls.add(link)

                # SE NON E' GIA' PRESENTE NELLA LISTA DEI link_to_scrape
                if not link in links_to_scrape_list:

                    # AGGIUNGO ALLA LISTA DEI link_to_scrape -> NON SERVE
                    links_to_scrape_list.append(link)

                    # AGGIUNGO ALLA LISTA DEI link_to_scrape
                    links_to_add.append(link)

            # SE SONO PRESENTI DEI LINK DA AGGIUNGERE ALLA LISTA
            if len(links_to_add):

                # PER OGNI LINK NUOVO DA AGGIUNGERE
                for link in links_to_add:

                    # INCREMENTO DI 1 IL TOTALE DEI LINK E QUINDI DEFINISCO IL SUO ID
                    total_link += 1

                    link_to_add = {
                        "url": link,
                        "link_id": total_link
                    }

                    # AGGIORNO IL FILE JSON links_to_scrape
                    update_links_to_scrape_info(
                        directory_links_to_scrape, total_link, last_id)
                    update_links_to_scrape_list(
                        link_to_add, directory_links_to_scrape)
            else:

                update_links_to_scrape_info(
                    directory_links_to_scrape, total_link, last_id)

            # ACCORPO I DATI RACCOLTI PER IL LINK NAVIGATO
            scraped_link = {
                "link_id": data[last_id]['link_id'],
                "url": data[last_id]['url'],
                "fetch_time" : today,
                "protocol": data[last_id]['url'].split("://")[0],
                "status_code": http_status,
                "internal_links": len(internal_urls),
                "external_links": len(external_urls),
                "located_in_sitemap": link_in_sitemap,
                "noindex": noindex,
                "nofollow": nofollow,
                "crawl_allowed_by_robotx.txt": robotstxt_allow,
                "core_web_vitals":
                [{"mobile": {
                    "largest_contentuful_paint": round(core_web_vitals_mobile['LCP'],2),
                    "largest_contentuful_paint_score": round(core_web_vitals_mobile['LCP_score'],2),
                    "cumulative_layout_shift": round(core_web_vitals_mobile['CLS'],2),
                    "cumulative_layout_shift_score": round(core_web_vitals_mobile['CLS_score'],2),
                    "first_input_delay": round(core_web_vitals_mobile['FID'],2),
                    "first_input_delay_score": round(core_web_vitals_mobile['FID_score'],2),
                    "speed_index" : round(core_web_vitals_mobile['speed_index'],2),
                    "speed_index_score" : round(core_web_vitals_mobile['speed_index_score'],2),
                    "overall_score" : round(core_web_vitals_mobile['overall_score'],2)
                },
                    "desktop": {
                    "largest_contentuful_paint": round(core_web_vitals_desktop['LCP'],2),
                    "largest_contentuful_paint_score": round(core_web_vitals_desktop['LCP_score'],2),
                    "cumulative_layout_shift": round(core_web_vitals_desktop['CLS'],2),
                    "cumulative_layout_shift_score": round(core_web_vitals_desktop['CLS_score'],2),
                    "first_input_delay": round(core_web_vitals_desktop['FID'],2),
                    "first_input_delay_score": round(core_web_vitals_desktop['FID_score'],2),
                    "speed_index" : round(core_web_vitals_desktop['speed_index'],2),
                    "speed_index_score" : round(core_web_vitals_desktop['speed_index_score'],2),
                    "overall_score" : round(core_web_vitals_desktop['overall_score'],2)
                }
                }],
                "keywords" : keywords,
                "title" : title,
                "lenguage": lang,
                "canonical_link" : canonical_check,
                "htag" : htags,
                "alt_tag" : alt_tag_check

            }

            # AGGIRONO IL FILE JSON scraped_links
            update_scraped_links(scraped_link, directory_scraped_links)
        
        except Exception as e:     # SE IL CRAWL FALLISCE, PROBABILMENTE E' PER UN PROBLEMA 4XX O 5XX
            # traceback.print_exc()     DEBUG
            directory_links_to_scrape = 'C:/xampp/htdocs/TESI/risorse/database_sites/' + \
            user + '/' + site_name + '/links_to_scrape.json'

            directory_scraped_links = 'C:/xampp/htdocs/TESI/risorse/database_sites/' + \
            user + '/' + site_name + '/scraped_links.json'

        
            with open(directory_links_to_scrape) as f:
                data = json.load(f)

            last_id = data[0]['url_to_scrape']
            url = data[last_id]['url']

            try:
                request = requests.head(url)
                http_status = request.status_code
            except Exception:
                # print(Exception) DEBUG
                http_status = "404"

            scraped_link = {
                "link_id": data[last_id]['link_id'],
                "url": url,
                "fetch_time" : today,
                "status_code": http_status,
            }

            # QUINDI MEMORIZZO SEMPLICEMENTE UN HTTP STATUS COME DATI RACCOLTI E AGGIORNO I FILE
            update_scraped_links(scraped_link, directory_scraped_links)
            update_links_to_scrape_info(directory_links_to_scrape)


#######################################     SCRIPT    ####################################################

site_name = sys.argv[1]
user = sys.argv[2]
max_urls = int(sys.argv[3])

# DEBUG 
# site_name = "www.unito.it"
# user = "UTENTE_1"
# max_urls = 1

crawl(max_urls, user, site_name)



    
