Quattor Data Warehouse (qdw)
============================

Description
-----------
Analyses the Quattor server profiles to provide information about them.

Information is in the form of either a frequency distribution or as the results of a search.

Settings
--------
Use the dw.conf file in the install directory to edit settings

    [dw]
    batchsize = 400         #sets the number of reports per bulk command, if curl error thrown try lowering
    singleindexlimit=50     #sets the number of machines for when the transfer from single to bulk indexing is made
    index=bulkindex         #name of the index ie. http://address:9200/index/type/...
    type=bulktype           #name of the type
    address=localhost       #sets the address of the elastic search server to use

NB. if the address of the server is not local then the profiles may not be local either in which case the command line option '-n' must be set unless the profiles are local.

Usage
-----
See man page or use --help

Help
----
If at any time the source of an error cannot be determined it is best to just delete the entire contents of the profiles folder and reimport them.

NB. This triggers a full re-index on next use, so will take some time.
