import os
import pyes
import re
import httplib
import json
import subprocess
import shutil
import ConfigParser

config = ConfigParser.ConfigParser()
config.read('dw.conf')

BATCHSIZE = config.getint('dw', 'batchsize')                 #size of each bulk indexing operation (no.profiles)
SINGLEINDEXLIMIT = config.getint('dw', 'singleindexlimit')   #number of profiles before bulk indexing takes over from single
INDEX = config.get('dw', 'index')
TYPE = config.get('dw', 'type')
ADDRESS = config.get('dw', 'address')                     #address of elastic search server

server = pyes.es.ES()   #initialises the conection to elastic search

def indexcount():
    countresult=json.loads(subprocess.check_output(['curl','-s','-XGET','http://'+ADDRESS+':9200/'+INDEX+'/'+TYPE+'/_count?']))
    return countresult['count']

def bulkindexer(jsonlist):
    chunkedjsonlist = [jsonlist[pos:pos + BATCHSIZE] for pos in xrange(0, len(jsonlist), BATCHSIZE)]    #breaks the list into seperate bulk operations of size BATCHSIZE to prevent timeout of curl
    for i in range(0,len(chunkedjsonlist)):
        fileoutput = open('indexlist'+str(i)+'.json','w')
        for profile in chunkedjsonlist[i]:                                  #creates the bulk file line by line, stripping newlines
            fileoutput.write('{ "index" : { "_id" : "'+profile+'" } }\n')   #adheres to elastic search bulk schema
            fileinput = open('Profiles/'+profile)
            for line in fileinput:
                line = line.rstrip('\n')
                fileoutput.write(line)
            fileoutput.write('\n')
        fileoutput.close()
        
    for f in os.listdir("."):
        if re.search('indexlist\d+\.json', f):                             #executes the bulk operations
            command='curl -s -XPOST '+ADDRESS+':9200/'+INDEX+'/'+TYPE+'/_bulk --data-binary @'+f+'>/dev/null'
            os.system(command)
            
    server.refresh(INDEX)       #updates the index ready for search
            
    for f in os.listdir("."):       #tidy up by removing the indexlist files
        if re.search('indexlist\d+\.json', f):
                os.remove(f)

def indexinstall():             #to be treated as a clean indexer, will eradicate any conflict and must be run on first time use
    shutil.rmtree('./Profiles/.git',ignore_errors=True)
    indexsettings=json.load(open('indexsettings.json'))
    server.delete_index_if_exists(INDEX)
    server.create_index(INDEX,indexsettings)
    regex = re.compile(".*\.json$")
    alljson = [ i for i in os.listdir("Profiles") if regex.match(i) ]   #only matches a1.b2.c3.json not ???.json~
    bulkindexer(alljson)
    os.chdir('Profiles')
    os.system("git init >/dev/null")
    os.system("git add *")
    os.system("git commit -m 'stamp' >/dev/null")
    os.chdir('..')
    
def updater():
    os.chdir('Profiles')
    os.system("git add *") #adds only modified and new files
    diff= subprocess.check_output(['git','diff','--name-only','--cached'])  #gets a list of what has changed
    regex = re.compile(".*\.json$")
    difflist = re.findall(regex, diff)
    dell= subprocess.check_output(['git','diff','--name-only'])  #gets a list of what has been deleted
    dellist = re.findall(regex, dell)
    os.system("git add -u") #adds all files inculding deleted ones
    os.system("git commit -m 'stamp' >/dev/null")
    os.chdir('..')
    if dellist != False:
        for i in dellist:
            print i
            server.delete(INDEX,TYPE,i)
        server.refresh(INDEX)
        
    if len(difflist)>SINGLEINDEXLIMIT:  #decides whether to use bulk operations
        bulkindexer(difflist)
    elif difflist != False:
        for i in difflist:
            openfile = open('./Profiles/'+i)
            contents = openfile.read()
            server.index(contents,INDEX,TYPE,i)
        server.refresh(INDEX)
    
    
def queryer(jsonquery):
    queryconn = httplib.HTTPConnection(ADDRESS+":9200")     #sets up a httpconection for 'curl style' requests
    queryconn.request('GET', '/'+INDEX+'/'+TYPE+'/_search?', json.dumps(jsonquery))
    queryresults = queryconn.getresponse()
    return json.load(queryresults)



