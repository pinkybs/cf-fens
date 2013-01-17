#include <unistd.h>
#include <netinet/in.h>
#include <netdb.h>
#include <iostream.h>

#include "Configuration.h"

#define SI_SUPPORT_IOSTREAMS
#include "SimpleIni.h"

#include "utils/text.h"

Configuration::Rebot::Rebot()
{
    sender = NULL;
    userAgent = NULL;
}

Configuration::Rebot::~Rebot()
{
    //delete not need check is NULL
    delete[] sender;
    delete[] userAgent;
}

Configuration::Network::Network()
{
    nb_conn = 20;
    dnsConn = 3;
    dnsCNameChains = 0;
    httpPort = 0;
    waitDuration = 60;
    proxyAddr = NULL;
}

Configuration::Network::~Network()
{
    delete proxyAddr;
}

void Configuration::Network::manageProxy(const char *proxy)
{
    if (proxy == NULL) {
        return;
    }

    char *tmp = newString(proxy);
    // host name and dns call
    struct hostent* hp;
    proxyAddr = new sockaddr_in;
    memset((char *) proxyAddr, 0, sizeof(struct sockaddr_in));
    char *p = strtok(tmp, ":");
    if (p) {
        if ((hp = gethostbyname(p)) == NULL) {
            endhostent();
            cerr << "Unable to find proxy ip address (" << p << ")\n";
            exit(1);
        } else {
            proxyAddr->sin_family = hp->h_addrtype;
            memcpy((char*) &proxyAddr->sin_addr, hp->h_addr, hp->h_length);
        }
        endhostent();
        // port number
        p = strtok(NULL, ":");
        if (p) {
            proxyAddr->sin_port = htons(atoi(p));
        }
    }
}

Configuration::Search::Search()
{
    domains = NULL;
    depthInSite = 5;
    maxTimeLimit = 86400;
    externalLinks = false;
}

Configuration::Search::~Search()
{
    delete domains;
}

void Configuration::Search::manageDomain(const char *limitToDomain)
{
    if (limitToDomain == NULL) {
        return;
    }

    char *tmp = newString(limitToDomain);
    char **posParse = &tmp;
    char *tok = nextToken(posParse);
    if (domains == NULL) {
        domains = new Vector<char> ;
    }
    while (tok != NULL) {
        domains->addElement(newString(tok));
        tok = nextToken(posParse);
    }

    deletep[] tmp;
}

void Configuration::Search::manageExt(const char *extensions)
{
    if (extensions == NULL) {
        return;
    }

    char *tmp = newString(extensions);
    char **posParse = &tmp;

    char *tok = nextToken(posParse);
    int l, i;
    while (tok != NULL) {
        l = strlen(tok);
        for (i = 0; i < l; i++) {
            tok[i] = tolower(tok[i]);
        }
        if (!matchPrivExt(tok)) {
            forbExt.addElement(newString(tok));
        }
        tok = nextToken(posParse);
    }

    deletep[] tmp;
}

Configuration::Mysql::Mysql()
{
    host = NULL;
    port = 3306;
    user = NULL;
    passwd = NULL;
    db = NULL;
}

Configuration::Mysql::~Mysql()
{
    delete[] host;
    delete[] user;
    delete[] passwd;
    delete[] db;
}

void Configuration::parse(char *configFile)
{
    //IsUtf8, MultiKey, MultiLine
    CSimpleIniA ini(true, false, true);
    SI_Error rc = ini.LoadFile("config.ini");

    if (rc < 0) {
        cerr << "Load configuration file: " << configFile << " failure!\n";
        exit(1);
    }

    //[rebot]
    rebot.sender = newString(ini.GetValue("rebot", "from", "fensbot@unspecified.mail"));
    rebot.userAgent = newString(ini.GetValue("rebot", "userAgent", "Fensbot"));

    //[network]
    network.nb_conn = (uint) ini.GetLongValue("network", "pagesConnexions", 20);
    network.dnsConn = (uint) ini.GetLongValue("network", "dnsConnexions", 3);
    network.dnsCNameChains = (uint) ini.GetLongValue("network", "dnsCNameChains", 0);
    network.httpPort = (unsigned short int) ini.GetLongValue("network", "httpPort", 0);
    network.waitDuration = (time_t) ini.GetLongValue("network", "waitDuration", 60);
    network.manageProxy(ini.GetValue("network", "proxy"));

    //[search]
    search.manageDomain(ini.GetValue("search", "limitToDomain"));
    search.depthInSite = (int8_t) ini.GetLongValue("search", "depthInSite", 5);
    search.maxTimeLimit = (uint) ini.GetLongValue("search", "maxTimeLimit", 86400);
    search.externalLinks = ini.GetBoolValue("search", "externalLinks", false);
    search.manageExt(ini.GetValue("search", "forbiddenExtensions"));

    //[mysql]
    mysql.host = newString(ini.GetValue("mysql", "host"));
    mysql.port = (uint) ini.GetLongValue("mysql", "port", 3306);
    mysql.user = newString(ini.GetValue("mysql", "user"));
    mysql.passwd = newString(ini.GetValue("mysql", "passwd"));
    mysql.db = newString(ini.GetValue("mysql", "db"));
}

Configuration::Configuration()
{
    parse("config.ini");
}

Configuration::Configuration(char *configFile)
{
    parse(configFile);
}

Configuration::~Configuration()
{
    //do nothing
}

