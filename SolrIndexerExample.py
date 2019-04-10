import urllib.request as urllib
import json
import time
from datetime import date

###
#
#  Fill in the URL to your Solr core and to your data catalog installation here
#
###
solr_core_url = 'https://www.example.com/solr/data_catalog'
data_catalog_base_url = 'https://www.example.com'
solr_submit_url = solr_core_url + '/update/json?commit=true&overwrite=true'
solr_remove_url = solr_core_url + '/update/?commit=true'

###
#
#  Fill in the proxy information
#
###
proxy_support = urllib.ProxyHandler({'http': 'proxy', 'https': 'proxy'})
opener = urllib.build_opener(proxy_support)
urllib.install_opener(opener)


def load_db_response(offset):
    db_output_url = data_catalog_base_url + '/api/Dataset/all_{0}.json?output_format=solr'.format(offset)
    db_response = urllib.urlopen(db_output_url)
    db_json_output = db_response.read()
    db_parsed_json = json.loads(db_json_output)
    return db_parsed_json


def load_solr_response(offset):
    solr_output_url = solr_core_url + '/select/?q=*:*&start={0}&rows=500&wt=json'.format(offset)
    solr_response = urllib.urlopen(solr_output_url)
    solr_json_output = solr_response.read()
    solr_parsed_json = json.loads(solr_json_output)
    return solr_parsed_json


def load_db_response_all():
    offset = 0
    all_db_result, cur_db_result = [], []
    _continue = True
    while _continue:
        start_time = time.time()
        cur_db_result = load_db_response(offset)
        if len(cur_db_result) == 0:
            _continue = False
        offset += 500
        all_db_result += cur_db_result
        print("%-10s %.2f seconds" % (offset, time.time() - start_time))
    print('Complete loading db')
    return all_db_result


def load_solr_response_all():
    offset = 0
    all_solr_result, cur_solr_result = [], []
    _continue = True
    while _continue:
        start_time = time.time()
        cur_solr_result = load_solr_response(offset)
        if len(cur_solr_result['response']['docs']) == 0:
            _continue = False
        offset += 500
        all_solr_result += cur_solr_result['response']['docs']
        print("%-10s %.2f seconds" % (offset, time.time() - start_time))
    print('Complete loading solr')
    return all_solr_result


def delete_index(db_parsed_json, solr_parsed_json):
    error_list = []
    for row in solr_parsed_json:
        if not [x for x in db_parsed_json if x['id'] == int(row['id'])]:
            to_solr = "{'delete': {'id': " + row['id'] + "}}"
            print("delete " + row['id'])
            request = urllib.Request(solr_remove_url, to_solr.encode("utf-8"), {'Content-Type': 'application/json'})
            try:
                urllib.urlopen(request)
            except urllib.HTTPError as e:
                error_list.append(e.read())

    print('\rDelete done')
    for error in error_list:
        print(error)


def insert_index(db_parsed_json, solr_parsed_json):
    error_list = []
    for row in db_parsed_json:
        if [x for x in solr_parsed_json if int(x['id']) == row['id']]:
            continue

        if row['dataset_end_date'] and row['dataset_start_date']:
            end = row['dataset_end_date']
            if end == 'Present':
                end = date.today().year + 1
            end = int(end)
            start = int(row['dataset_start_date'])
            if start == end:
                row['dataset_years'] = [str(start) + '-01-01T00:00:00Z', str(end) + '-01-01T00:00:00Z']
            else:
                years = range(start, end)
                years = [str(v) + '-01-01T00:00:00Z' for v in years]
                row['dataset_years'] = years
        for item in list(row):
            if not row[item]:
                del row[item]
        if row.get('date_added', None):
            from_symfony = row['date_added']['date']
            solr_date = from_symfony.split()[0].strip() + 'T00:00:00Z'
            row['date_added'] = solr_date
        to_solr = json.dumps(row)
        to_solr = "[" + to_solr + "]"
        print(to_solr)
        request = urllib.Request(solr_submit_url, to_solr.encode("utf-8"), {'Content-Type': 'application/json'})

        try:
            urllib.urlopen(request)
        except urllib.HTTPError as e:
            error_list.append(e.read())

    print('\rUpdate done')
    for error in error_list:
        print(error)


def main():
    db_parsed_json = load_db_response_all()
    solr_parsed_json = load_solr_response_all()
    delete_index(db_parsed_json, solr_parsed_json)
    insert_index(db_parsed_json, solr_parsed_json)


if __name__ == '__main__':
    main()
