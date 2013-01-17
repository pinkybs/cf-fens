#ifndef __FENS_TYPES_H__
#define __FENS_TYPES_H__

// Size of the HashSize (max number of urls that can be fetched)
#define FENS_HASH_SIZE 64000000                 //hashSize

// Size of the duplicate hashTable
#define FENS_DUP_SIZE FENS_HASH_SIZE            //dupSize
#define FENS_DUP_FILE "dupfile.bak"             //dupFile

// Size of the arrays of Sites in main memory
#define FENS_NAMEDSITE_LIST_SIZE 20000          //namedSiteListSize
#define FENS_IPSITE_LIST_SIZE 10000             //IPSiteListSize

// Max number of urls in ram
#define FENS_RAM_URLS 100000                    //ramUrls
// this should allow less dns call
#define FENS_MAX_IP_URLS 80000                  //maxIPUrls
// Max number of urls per site in Url
// must fit in uint8_t
#define FENS_MAX_URLS_BY_SITE 254               //maxUrlsBySite

// time out when reading a page (in sec)
// default time out
#define FENS_TIMEOUT_PAGE 30                    //timeoutPage
// number of bytes for 1 more sec
#define FENS_TIMEOUT_INCR 2000                  //timeoutIncr
// How long do we keep dns answers and robots.txt
#define FENS_DNS_VALID_TIME 2*24*3600           //dnsValidTime

// Maximum size of a page
#define FENS_MAX_PAGE_SIZE 800000               //maxPageSize
#define FENS_NEARLY_FULL_PAGE 790000            //nearlyFullPage

// Max size of a mysql query result number
#define FENS_MYSQL_MAX_RESULT_SIZE 500

// Maximum size of a robots.txt that is read
// the value used is min(FENS_MAX_PAGE_SIZE, FENS_MAX_ROBOTS_SIZE)
#define FENS_MAX_ROBOTS_SIZE 10000              //maxRobotsSize

// How many forbidden items do we accept in a robots.txt
#define FENS_MAX_ROBOTS_ITEM 100                //maxRobotsItem

// file name used for storing urls on disk
#define FENS_FIFO_FILE "fifo"                   //fifoFile
#define FENS_FIFO_WAIT_FILE "fifowait"          //fifoFileWait

// number of urls per file on disk
// should be equal to FENS_RAM_URLS for good interaction with restart
#define FENS_URL_BY_FILE FENS_RAM_URLS          //urlByFile

// Size of the buffer used to read sockets
#define FENS_BUF_SIZE 16384                     //BUF_SIZE
#define FENS_STRING_SIZE 1024                   //STRING_SIZE

// Max size for a url
#define FENS_MAX_URL_SIZE 2083                  //maxUrlSize
// max size for the name of a site
#define FENS_MAX_SITENAME_SIZE 40               //maxSiteSize
// max size for cookies
#define FENS_MAX_COOKIE_SIZE 1024               //maxCookieSize

// Standard size of a fifo in a Site
#define FENS_STD_VECTOR_SIZE 100                //StdVectSize

// maximum number of input connections
#define FENS_MAX_INPUT 5                        //maxInput

// if we save files, how many files per directory and where
#define FENS_FILES_PER_DIR 2000                 //filesPerDir
#define FENS_SAVE_DIR "save/"                   //saveDir
// for MIRROR_SAVE
#define FENS_INDEX_FILE "index.html"            //indexFile
// for MIRROR_SAVE
#define FENS_NB_DIR 1000                        //nbDir
// options for FENS_OPT_SPECIFICSEARCH (except with FENS_OPT_DEFAULT_SPECIFIC)
#define FENS_SPEC_DIR "specific/"               //specDir
#define FENS_MAX_SPEC_SIZE 5000000              //maxSpecSize

// Various reasons of error when getting a page
#define FENS_NB_ANSWERS 16                      //nbAnswers
enum FetchError
{
    success,            // 0
    noDNS,              // 1
    noConnection,       // 2
    forbiddenRobots,    // 3
    timeout,            // 4
    badType,            // 5
    tooBig,             // 6
    err30X,             // 7
    err40X,             // 8
    earlyStop,          // 9
    duplicate,          // 10
    fastRobots,         // 11
    fastNoConn,         // 12
    fastNoDns,          // 13
    tooDeep,            // 14
    urlDup              // 15
};

// standard types
typedef unsigned int uint;

#endif // __FENS_TYPES_H__
