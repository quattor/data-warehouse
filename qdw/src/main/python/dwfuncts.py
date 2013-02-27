import os
import re
import httplib
import json
import subprocess
import shutil

def check_output(cmd):
    (o, e) = subprocess.Popen(cmd, stdout=subprocess.PIPE).communicate()
    if o is not None and not e:
        return o
    else:
        print "ERROR: %s returned %s, %s" % (cmd, o, e)
        return None

def indexcount(logger):
    logger.debug("dwfuncts.indexcount: start")
    countresult=json.loads(check_output(['curl','-s','-XGET','http://%s:%s/%s/%s/_count?' % (ADDRESS, PORT, INDEX, TYPE)]))
    return countresult['count']

def bulkindexer(logger, server, jsonlist):
    logger.debug("dwfuncts.bulkindexer: indexing %s" % (jsonlist))
    chunkedjsonlist = [jsonlist[pos:pos + BATCHSIZE] for pos in xrange(0, len(jsonlist), BATCHSIZE)]    #breaks the list into seperate bulk operations of size BATCHSIZE to prevent timeout of curl
    for i in range(0,len(chunkedjsonlist)):
        logger.debug("dwfuncts.bulkindexer: loop counter %s" % (i))
        filename = 'indexlist'+str(i)+'.json'
        logger.debug("dwfuncts.bulkindexer: loop filename %s" % (filename))
        fileoutput = open(filename,'w')
        for profile in chunkedjsonlist[i]:                                  #creates the bulk file line by line, stripping newlines
            logger.debug("dwfuncts.bulkindexer: loop profile %s" % (profile))
            fileoutput.write('{ "index" : { "_id" : "'+profile+'" } }\n')   #adheres to elastic search bulk schema
            fileinput = open('Profiles/'+profile)
            for line in fileinput:
                line = line.rstrip('\n')
                fileoutput.write(line)
            fileoutput.write('\n')
        fileoutput.close()
        
    for f in os.listdir("."):
        if re.search('indexlist\d+\.json', f):                             #executes the bulk operations
            logger.debug("dwfuncts.bulkindexer: submitting bulk file %s" % (f))
            command='curl -s -XPOST %s:%s/%s/%s/_bulk --data-binary @%s >/dev/null' % (ADDRESS, PORT, INDEX, TYPE, f)
            os.system(command)
            
    server.refresh(INDEX)       #updates the index ready for search
            
    for f in os.listdir("."):       #tidy up by removing the indexlist files
        if re.search('indexlist\d+\.json', f):
                os.remove(f)

def indexinstall(logger, server):             #to be treated as a clean indexer, will eradicate any conflict and must be run on first time use
    logger.debug("dwfuncts.indexinstall: start")
    shutil.rmtree('./Profiles/.git',ignore_errors=True)
    indexsettings=json.load(open('indexsettings.json'))
    logger.debug("dwfuncts.indexinstall: read index settings: " % (indexsettings))
    server.delete_index_if_exists(INDEX)
    server.create_index(INDEX,indexsettings)
    regex = re.compile(".*\.json$")
    alljson = [ i for i in os.listdir("Profiles") if regex.match(i) ]   #only matches a1.b2.c3.json not ???.json~
    logger.debug("dwfuncts.indexinstall: matched profiles: " % (alljson))
    bulkindexer(logger, server, alljson)
    os.chdir('Profiles')
    os.system("git init >/dev/null")
    os.system("git add *")
    os.system("git commit -m 'stamp' >/dev/null")
    os.chdir('..')
    
def updater(logger, server):
    logger.debug("dwfuncts.updater: start")
    os.chdir('Profiles')
    os.system("git add *") #adds only modified and new files
    diff = check_output(['git','diff','--name-only','--cached'])  #gets a list of what has changed
    logger.debug("dwfuncts.updater: changed profiles appear to be: %s" % (diff))
    regex = re.compile(".*\.json$")
    difflist = re.findall(regex, diff)
    dell= check_output(['git','diff','--name-only'])  #gets a list of what has been deleted
    logger.debug("dwfuncts.updater: deleted profiles appear to be: %s" % (diff))
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
        logger.debug("dwfuncts.updater: using bulk index operations")
        bulkindexer(logger, difflist)
    elif difflist != False:
        logger.debug("dwfuncts.updater: using normal index operations, difflist = %s" % (difflist))
        for i in difflist:
            openfile = open('./Profiles/'+i)
            logger.debug("dwfuncts.updater: opened %s" % (i))
            contents = openfile.read()
            s = server.index(contents,INDEX,TYPE,i)
            logger.debug("dwfuncts.updater: indexing result: %s" % (s))
        server.refresh(INDEX)
    
    
def queryer(logger, jsonquery):
    logger.debug("dwfuncts.queryer: start")
    queryconn = httplib.HTTPConnection("%s:%s" % (ADDRESS, PORT))     #sets up a httpconection for 'curl style' requests
    request = '/%s/%s/_search?' % (INDEX, TYPE)
    logger.debug("dwfuncts.queryer: GET %s %s" % (request, jsonquery))
    queryconn.request('GET', request, json.dumps(jsonquery))
    queryresults = queryconn.getresponse()
    logger.debug("dwfuncts.queryer: query returned %s" % (queryresults))
    return json.load(queryresults)



