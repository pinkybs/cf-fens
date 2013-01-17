#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <errno.h>
#include <fcntl.h>
#include <iostream.h>
#include <string.h>
#include <adns.h>
#include <netdb.h>
#include <string>
#include <signal.h>
#include <ctype.h>

#include "options.h"

#include "types.h"
#include "global.h"
#include "utils/text.h"
#include "utils/Fifo.h"
#include "utils/debug.h"
#include "fetch/site.h"
#include "interf/output.h"
#include "interf/input.h"

#ifdef FENS_OPT_START_URL_FROM_DATABASE
#include "mysql.h"
#include "utils/SiteDB.h"
#endif // FENS_OPT_START_URL_FROM_DATABASE
using namespace fens;

///////////////////////////////////////////////////////////
// Struct global
///////////////////////////////////////////////////////////

// define all the static variables
uint global::ProcessNum = 0;
uint global::ProcessTotalNum = 0;
MysqlConnectConfig global::mysqlConConfig;
time_t global::now;
hashTable *global::seen;
#ifdef FENS_OPT_NO_DUP
hashDup *global::hDuplicate;
#endif // FENS_OPT_NO_DUP
SyncFifo<url> *global::URLsPriority;
SyncFifo<url> *global::URLsPriorityWait;
uint global::readPriorityWait = 0;
PersistentFifo *global::URLsDisk;
PersistentFifo *global::URLsDiskWait;
uint global::readWait = 0;
IPSite *global::IPSiteList;
NamedSite *global::namedSiteList;
Fifo<IPSite> *global::okSites;
Fifo<NamedSite> *global::dnsSites;
Connexion *global::connexions;
adns_state global::ads;
uint global::nbDnsCalls = 0;
uint global::nbDnsCNameChainsCalls = 0;
ConstantSizedFifo<Connexion> *global::freeConns;
#ifdef FENS_OPT_THREAD_OUTPUT
ConstantSizedFifo<Connexion> *global::userConns;
#endif
Interval *global::inter;
int8_t global::depthInSite;
uint global::maxTimeLimit = 0;
bool global::externalLinks = true;
time_t global::waitDuration;
char *global::userAgent;
char *global::sender;
char *global::headers;
char *global::headersRobots;
sockaddr_in *global::proxyAddr;
Vector<char> *global::domains;
Vector<char> global::forbExt;
uint global::nb_conn;
uint global::dnsConn;
uint global::dnsCNameChains;
unsigned short int global::httpPort;
unsigned short int global::inputPort;
struct pollfd *global::pollfds;
uint global::posPoll;
uint global::sizePoll;
short *global::ansPoll;
int global::maxFds;
#ifdef FENS_OPT_MAX_BANDWIDTH
long int global::remainBand = FENS_OPT_MAX_BANDWIDTH;
#endif // FENS_OPT_MAX_BANDWIDTH
int global::IPUrl = 0;

/** Constructor : initialize almost everything
 * Everything is read from the config file (fensbot.conf by default)
 */
global::global(int argc, char *argv[])
{
    char *configFile = "fensbot.conf";
#ifdef FENS_OPT_RELOAD
    bool reload = true;
#else
    bool reload = false;
#endif //FENS_OPT_RELOAD
    now = time(NULL);
    // verification of arguments
    int pos = 1;
    while (pos < argc) {
        if (!strcmp(argv[pos], "-c") && argc > pos + 1) {
            configFile = argv[pos + 1];
            pos += 2;
        } else if (!strcmp(argv[pos], "-n") && argc > pos + 1) {
            // 3/5
            // ProcessNum = 3
            // ProcessTotalNum = 5
            char *pch = strtok(argv[pos + 1], "/");
            if (pch != NULL) {
                ProcessNum = (uint) strtoul(pch, NULL, 0);
                pch = strtok(NULL, "/");
                if (pch != NULL) {
                    ProcessTotalNum = (uint) strtoul(pch, NULL, 0);
                }
            }
            pos += 2;
        } else if (!strcmp(argv[pos], "-t") && argc > pos + 1) {
            maxTimeLimit = (uint) strtoul(argv[pos + 1], NULL, 0);
            pos += 2;
        } else if (!strcmp(argv[pos], "-scratch")) { // start from scratch (a new)
            reload = false;
            pos++;
        } else if (!strcmp(argv[pos], "-o") && argc > pos + 1) {
            freopen(argv[pos + 1], "w", stdout);
            pos += 2;
        } else {
            break;
        }
    }
    if (pos != argc) {
        cerr << "usage : " << argv[0];
        cerr << " [-c configFile] [-n processNumber] [-t maxTimeLimit] [-scratch] [-o outputTextFile]\n";
        exit(1);
    }

    // Standard values
    waitDuration = 60;
    depthInSite = 5;
    userAgent = "Fensbot";
    sender = "fensbot@unspecified.mail";
    nb_conn = 20;
    dnsConn = 3;
    dnsCNameChains = 0;
    httpPort = 0;
    inputPort = 0; // by default, no input available
    proxyAddr = NULL;
    domains = NULL;
    // FIFOs
    char fifoBuffer[100];
    sprintf(fifoBuffer, "%s_%d_", FENS_FIFO_FILE, ProcessNum);
    URLsDisk = new PersistentFifo(reload, fifoBuffer);
    sprintf(fifoBuffer, "%s_%d_", FENS_FIFO_WAIT_FILE, ProcessNum);
    URLsDiskWait = new PersistentFifo(reload, fifoBuffer);
    URLsPriority = new SyncFifo<url> ;
    URLsPriorityWait = new SyncFifo<url> ;
    inter = new Interval(FENS_RAM_URLS);
    namedSiteList = new NamedSite[FENS_NAMEDSITE_LIST_SIZE];
    IPSiteList = new IPSite[FENS_IPSITE_LIST_SIZE];
    okSites = new Fifo<IPSite> (2000);
    dnsSites = new Fifo<NamedSite> (2000);
    seen = new hashTable(!reload);
#ifdef FENS_OPT_NO_DUP
    hDuplicate = new hashDup(FENS_DUP_SIZE, FENS_DUP_FILE, !reload);
#endif // FENS_OPT_NO_DUP
    // Read the configuration file
    crash("Read the configuration file");
    parseFile(configFile);
    // Initialize everything
    crash("Create global values");
    // Headers
    FensString strtmp;
    strtmp.addString("\r\nUser-Agent: ");
    strtmp.addString(userAgent);
    strtmp.addString(" ");
    strtmp.addString(sender);
#ifdef FENS_OPT_SPECIFICSEARCH
    strtmp.addString("\r\nAccept: text/html");
    int i=0;
    while (contentTypes[i] != NULL) {
        strtmp.addString(", ");
        strtmp.addString(contentTypes[i]);
        i++;
    }
#elif !defined(FENS_OPT_IMAGES) && !defined(FENS_OPT_ANYTYPE)
    strtmp.addString("\r\nAccept: text/html");
#endif // FENS_OPT_SPECIFICSEARCH
    strtmp.addString("\r\n\r\n");
    headers = strtmp.giveString();
    // Headers robots.txt
    strtmp.recycle();
    strtmp.addString("\r\nUser-Agent: ");
    strtmp.addString(userAgent);
    strtmp.addString(" (");
    strtmp.addString(sender);
    strtmp.addString(")\r\n\r\n");
    headersRobots = strtmp.giveString();
#ifdef FENS_OPT_THREAD_OUTPUT
    userConns = new ConstantSizedFifo<Connexion>(nb_conn);
#endif
    freeConns = new ConstantSizedFifo<Connexion> (nb_conn);
    connexions = new Connexion[nb_conn];
    for (uint i = 0; i < nb_conn; i++) {
        freeConns->put(connexions + i);
    }
    // init poll structures
    sizePoll = nb_conn + FENS_MAX_INPUT;
    pollfds = new struct pollfd[sizePoll];
    posPoll = 0;
    maxFds = sizePoll;
    ansPoll = new short[maxFds];
    // init non blocking dns calls
    adns_initflags flags = adns_initflags(adns_if_nosigpipe
            | adns_if_noerrprint);
    adns_init(&ads, flags, NULL);
    // call init functions of all modules
    initSpecific();
    initInput();
    initOutput();
    initSite();
    // let's ignore SIGPIPE
    static struct sigaction sn, so;
    sigemptyset(&sn.sa_mask);
    sn.sa_flags = SA_RESTART;
    sn.sa_handler = SIG_IGN;
    if (sigaction(SIGPIPE, &sn, &so)) {
        cerr << "Unable to disable SIGPIPE : " << strerror(errno) << endl;
    }
}

/** Destructor : never used because the program should never end !
 */
global::~global()
{
    assert(false);
}

/** parse configuration file */
void global::parseFile(char *file)
{
    int fds = open(file, O_RDONLY);
    if (fds < 0) {
        cerr << "cannot open config file (" << file << ") : "
                << strerror(errno) << endl;
        exit(1);
    }
    char *tmp = readfile(fds);
    close(fds);
    // suppress commentary
    bool eff = false;
    for (int i = 0; tmp[i] != 0; i++) {
        switch (tmp[i]) {
            case '\n':
                eff = false;
                break;
            case '#':
                eff = true; // no break !!!
            default:
                if (eff)
                    tmp[i] = ' ';
        }
    }
    char *posParse = tmp;
    char *tok = nextToken(&posParse);
    while (tok != NULL) {
        if (!strcasecmp(tok, "UserAgent")) {
            userAgent = newString(nextToken(&posParse));
        } else if (!strcasecmp(tok, "From")) {
            sender = newString(nextToken(&posParse));
        } else if (!strcasecmp(tok, "startUrl")) {
            tok = nextToken(&posParse);
#ifndef FENS_OPT_START_URL_FROM_DATABASE
            url *u = new url(tok, global::depthInSite, (url *) NULL);
            if (u->isValid()) {
                check(u);
            } else {
                cerr << "the start url " << tok << " is invalid\n";
                exit(1);
            }
#endif // undefine FENS_OPT_START_URL_FROM_DATABASE
        } else if (!strcasecmp(tok, "waitduration")) {
            tok = nextToken(&posParse);
            waitDuration = atoi(tok);
        } else if (!strcasecmp(tok, "proxy")) {
            // host name and dns call
            tok = nextToken(&posParse);
            struct hostent* hp;
            proxyAddr = new sockaddr_in;
            memset((char *) proxyAddr, 0, sizeof(struct sockaddr_in));
            if ((hp = gethostbyname(tok)) == NULL) {
                endhostent();
                cerr << "Unable to find proxy ip address (" << tok << ")\n";
                exit(1);
            } else {
                proxyAddr->sin_family = hp->h_addrtype;
                memcpy((char*) &proxyAddr->sin_addr, hp->h_addr, hp->h_length);
            }
            endhostent();
            // port number
            tok = nextToken(&posParse);
            proxyAddr->sin_port = htons(atoi(tok));
        } else if (!strcasecmp(tok, "pagesConnexions")) {
            tok = nextToken(&posParse);
            nb_conn = atoi(tok);
        } else if (!strcasecmp(tok, "dnsConnexions")) {
            tok = nextToken(&posParse);
            dnsConn = atoi(tok);
        } else if (!strcasecmp(tok, "dnsCNameChains")) {
            tok = nextToken(&posParse);
            dnsCNameChains = atoi(tok);
        } else if (!strcasecmp(tok, "httpPort")) {
            tok = nextToken(&posParse);
            httpPort = atoi(tok);
        } else if (!strcasecmp(tok, "inputPort")) {
            tok = nextToken(&posParse);
            inputPort = atoi(tok);
        } else if (!strcasecmp(tok, "depthInSite")) {
            tok = nextToken(&posParse);
            depthInSite = atoi(tok);
        } else if (!strcasecmp(tok, "maxTimeLimit")) {
            tok = nextToken(&posParse);
            uint t = (uint)atoi(tok);
            if (maxTimeLimit == 0) {
                maxTimeLimit = t;
            } else {
                if (t < maxTimeLimit) {
                    maxTimeLimit = t;
                }
            }
        } else if (!strcasecmp(tok, "limitToDomain")) {
            manageDomain(&posParse);
        } else if (!strcasecmp(tok, "forbiddenExtensions")) {
            manageExt(&posParse);
        } else if (!strcasecmp(tok, "noExternalLinks")) {
            externalLinks = false;
        } else if (!strcasecmp(tok, "mysql_host")) {
            mysqlConConfig.host = newString(nextToken(&posParse));
        } else if (!strcasecmp(tok, "mysql_port")) {
            tok = nextToken(&posParse);
            mysqlConConfig.port = atoi(tok);
        } else if (!strcasecmp(tok, "mysql_user")) {
            mysqlConConfig.user = newString(nextToken(&posParse));
        } else if (!strcasecmp(tok, "mysql_passwd")) {
            mysqlConConfig.passwd = newString(nextToken(&posParse));
        } else if (!strcasecmp(tok, "mysql_db")) {
            mysqlConConfig.db = newString(nextToken(&posParse));
        } else {
            cerr << "bad configuration file : " << tok << "\n";
            exit(1);
        }
        tok = nextToken(&posParse);
    }
    delete[] tmp;
}

/** read the domain limit */
void global::manageDomain(char **posParse)
{
    char *tok = nextToken(posParse);
    if (domains == NULL) {
        domains = new Vector<char> ;
    }
    while (tok != NULL && strcasecmp(tok, "end")) {
        domains->addElement(newString(tok));
        tok = nextToken(posParse);
    }
    if (tok == NULL) {
        cerr << "Bad configuration file : no end to limitToDomain\n";
        exit(1);
    }
}

/** read the forbidden extensions */
void global::manageExt(char **posParse)
{
    char *tok = nextToken(posParse);
    while (tok != NULL && strcasecmp(tok, "end")) {
        int l = strlen(tok);
        int i;
        for (i = 0; i < l; i++) {
            tok[i] = tolower(tok[i]);
        }
        if (!matchPrivExt(tok))
            forbExt.addElement(newString(tok));
        tok = nextToken(posParse);
    }
    if (tok == NULL) {
        cerr << "Bad configuration file : no end to forbiddenExtensions\n";
        exit(1);
    }
}

/** make sure the max fds has not been reached */
void global::verifMax(int fd)
{
    if (fd >= maxFds) {
        int n = 2 * maxFds;
        if (fd >= n) {
            n = fd + maxFds;
        }
        short *tmp = new short[n];
        for (int i = 0; i < maxFds; i++) {
            tmp[i] = ansPoll[i];
        }
        for (int i = maxFds; i < n; i++) {
            tmp[i] = 0;
        }
        delete (ansPoll);
        maxFds = n;
        ansPoll = tmp;
    }
}

#ifdef FENS_OPT_START_URL_FROM_DATABASE
void global::loadStartUrlsFromDB() {
    SiteDB sitedb(mysqlConConfig.host, mysqlConConfig.user, mysqlConConfig.passwd, mysqlConConfig.db);

    std::vector<MYSQL_ROW> v;
    int num = sitedb.getSiteList(v, global::ProcessNum, global::ProcessTotalNum);
    //printf("urls number: %d, vector size: %d\n", num, v.size());
    if (num > 0) {
        for(int i = 0, len = v.size(); i < len; i++) {
            MYSQL_ROW row = v[i];
            //row -- id [0], name [1], url [2], toppage [3]
            //printf("id:%s\tttoppage:%s\n", row[0], row[3]);
            url *u = new url(atoi(row[0]), row[3], global::depthInSite, (url *) NULL);
            if (u->isValid()) {
                check(u);
            }
        }
    }
}
#endif // FENS_OPT_START_URL_FROM_DATABASE
///////////////////////////////////////////////////////////
// Struct Connexion
///////////////////////////////////////////////////////////

/** put Connection in a coherent state
 */
Connexion::Connexion()
{
    state = emptyC;
    parser = NULL;
}

/** Destructor : never used : we recycle !!!
 */
Connexion::~Connexion()
{
    assert(false);
}

/** Recycle a connexion
 */
void Connexion::recycle()
{
    delete parser;
    request.recycle();
}

///////////////////////////////////////////////////////////
// Struct MysqlConnectConfig
///////////////////////////////////////////////////////////

MysqlConnectConfig::MysqlConnectConfig()
{
    port = 3309;
    unix_socket = NULL;
}
