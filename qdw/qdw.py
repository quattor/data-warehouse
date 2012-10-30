#!/usr/bin/python
import argparse
import json
import os
import dwfuncts

parser = argparse.ArgumentParser(description="Search for data from within the server profiles. The program treats the profiles as file systems therefore the path argument should reflect this. For example when producing a distribution of 'driver' the 'path' would be hardware/cards/nic/eth0/driver.")
group = parser.add_mutually_exclusive_group()
group.add_argument('-d','--distributionofx',nargs=1,metavar='path', type=str, help="Produces a frequency distribution of all the values within 'path' (as json formatted string if --prettyprint not used)")
group.add_argument('-f','--frequencyofx', nargs=2, metavar=('path', 'value'), help="Produces a list of machines, all of which have 'value' within 'path' (as json formatted string if --prettyprint not used)")
parser.add_argument('-p','--prettyprint', action='store_true',help='Makes general outputs readable on the command line, not recommended for use as part of API')
parser.add_argument('-n','--noindex', action='store_true', default=False, help='Forces the program not to index any profiles, this will increase speed but may not yeild accurate results. (NB.is neccesary if profiles are not stored locally)')
args = parser.parse_args()

if not(args.noindex):
    if not os.path.isdir('Profiles/.git'):
        dwfuncts.indexinstall() #inits index and populates
    else:
        dwfuncts.updater() #updates the index
        
if (args.distributionofx is not(None)):
    path = args.distributionofx[0].strip('/').replace("/",".")
    count = dwfuncts.indexcount()
    jsonquery={
                "fields":[""], #dont return any of the source
                "size":count, #gets number of files in the profiles dir and set as the max no. results to return
                "query" : {
                    "match_all" : {}
                },
                "facets" : {
                    "tag" : {
                        "terms" : {
                            "field" : path
                        }
                        }
                    }
            }
    raw=dwfuncts.queryer(jsonquery)
    mixedlist = raw['facets']['tag']['terms']   #navigate to the terms feild within the results
    array=[]
    for i in range(0,len(mixedlist)):
        temp=[mixedlist[i]["term"],int(mixedlist[i]["count"])]  #organises
        array.append(temp)
    if raw['facets']['tag']['missing'] != 0:
        array.append(["No Data",raw['facets']['tag']['missing']])
    if args.prettyprint:
        print 'Value : Frequency'
        for i in array:
            print i
    else:
        print (json.dumps(array))
        
elif (args.frequencyofx is not(None)):
    path = args.frequencyofx[0].strip('/').replace("/",".")
    value = args.frequencyofx[1]
    count = dwfuncts.indexcount()
    jsonquery={
                "fields":[""],
                "size":count,
                "query" : {
                    "term" : {path : value}
                        }
            }
    raw=dwfuncts.queryer(jsonquery)
    machines=[ i["_id"].rstrip('.json') for i in raw["hits"]["hits"] ] #gets all the ids of the resulsts and strip trailing .json
    if args.prettyprint:
        hits=raw["hits"]["total"]
        print 'hits : '+str(hits)
        print 'machines:'
        for i in machines:
            print i
    else:
        print (json.dumps(machines))


