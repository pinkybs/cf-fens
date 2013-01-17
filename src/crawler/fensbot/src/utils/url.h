#ifndef __FENS_URL_H__
#define __FENS_URL_H__

#include <netinet/in.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdlib.h>

#include "types.h"

bool fileNormalize(char *file);

class url
{
private:
#ifdef FENS_OPT_URL_SITEID
    /* for fens url, site_id */
    uint siteId;
#endif
    char *host;
    char *file;
    uint16_t port; // the order of variables is important for physical size
    bool security;
    int8_t depth;
    char *redirFrom;
    /* parse the url */
    void parse(char *s);
    /** parse a file with base */
    void parseWithBase(char *u, url *base);
    /* normalize file name */
    bool normalize(char *file);
    /* Does this url starts with a protocol name */
    bool isProtocol(char *s);
    /* constructor used by giveBase */
    url(char *host, uint port, char *file);
    char* redirFromHost;
    /* give means that you have to delete the string yourself */
    char* giveHost(char* u);

public:
    /* Constructor : Parses an url (u is deleted) */
    url(char *u, int8_t depth, url *base);

#ifdef FENS_OPT_URL_SITEID
    /* for fens */
    url (uint siteId, char *u, int8_t depth, url *base);
#endif

    /* constructor used by input */
    url(char *line, int8_t depth);

    /* Constructor : read the url from a file (cf serialize) */
    url(char *line);

    /* Destructor */
    ~url();

    /* inet addr (once calculated) */
    struct in_addr addr;

    /* Is it a valid url ? */
    bool isValid();

    /* print an URL */
    void print();

#ifdef FENS_OPT_URL_SITEID
    /* return the site id */
    inline uint getSiteId() {return siteId;}
#endif

    /* return the host */
    inline char *getHost()
    {
        return host;
    }

    /* return the port */
    inline uint getPort()
    {
        return port;
    }

    /* return the file */
    inline char *getFile()
    {
        return file;
    }

    /* is security url SSL */
    inline bool isSecurity()
    {
        return security;
    }

    /** Depth in the Site */
    inline int8_t getDepth()
    {
        return depth;
    }

    inline char *getRedirFrom()
    {
        return redirFrom;
    }

    void setRedirFrom(char *from);

    /* Set depth to max if we are at an entry point in the site
     * try to find the ip addr
     * answer false if forbidden by robots.txt, true otherwise */
    bool initOK(url *from);

    /** return the base of the url
     * give means that you have to delete the string yourself
     */
    url *giveBase();

    /** return a char * representation of the url
     * give means that you have to delete the string yourself
     */
    char *giveUrl();

    /** write the url in a buffer
     * buf must be at least of size FENS_MAX_URL_SIZE
     * returns the size of what has been written (not including '\0')
     */
    int writeUrl(char *buf);

    /* serialize the url for the Persistent Fifo */
    char *serialize();

    /* very thread unsafe serialisation in a static buffer */
    char *getUrl();

    bool isSameHost(char* url);
    bool isSameHost(url* u);

    /* return a hashcode for the host of this url */
    uint hostHashCode();

    /* return a hashcode for this url */
    uint hashCode();

#ifdef FENS_OPT_URL_TAGS
    /* tag associated to this url */
    uint tag;
#endif // FENS_OPT_URL_TAGS
#ifdef FENS_OPT_COOKIES
    /* cookies associated with this page */
    char *cookie;
    void addCookie(char *header);
#else // FENS_OPT_COOKIES
    inline void addCookie(char *header)
    {
    }
#endif // FENS_OPT_COOKIES
};

#endif // __FENS_URL_H__
