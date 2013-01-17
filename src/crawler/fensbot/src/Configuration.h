#ifndef __FENS_CONFIGURATION_H_
#define __FENS_CONFIGURATION_H_

#include <sys/socket.h>

#include <adns.h>

#include "types.h"
#include "utils/Vector.h"

struct Configuration
{
    struct Rebot
    {
        char *sender;
        char *userAgent;

        Rebot();
        ~Rebot();
    };

    struct Network
    {
        /** number of parallel connexions
         * your kernel must support a little more than nb_conn file descriptors
         */
        uint nb_conn;
        /** number of parallel dns calls */
        uint dnsConn;
        /** The max length of dns cname chains */
        uint dnsCNameChains;
        /** port on which is launched the http statistic webserver */
        unsigned short int httpPort;
        /** how many seconds should we wait beetween 2 calls at the same server
         * 0 if you are only on a personnal server, >=30 otherwise
         */
        time_t waitDuration;
        /* internet address of the proxy (if any) */
        sockaddr_in *proxyAddr;

        void manageProxy(const char *proxy);

        Network();
        ~Network();
    };

    struct Search
    {
        /** Limit to domain */
        Vector<char> *domains;
        /** How deep should we go inside a site */
        int8_t depthInSite;
        uint maxTimeLimit;
        bool externalLinks;
        /** forbidden extensions
         * extensions which are allways to avoid : .ps, .pdf...
         */
        Vector<char> forbExt;

        void manageDomain(const char *domains);
        void manageExt(const char *extensions);

        Search();
        ~Search();
    };


    struct Mysql
    {
        char *host;
        uint port;
        char *user;
        char *passwd;
        char *db;

        Mysql();
        ~Mysql();
    };

    Rebot rebot;
    Network network;
    Search search;
    Mysql mysql;

    void parse(char *configFile);

    Configuration();
    Configuration(char *configFile);
    ~Configuration();
};

#endif // __FENS_CONFIGURATION_H_
