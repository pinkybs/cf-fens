/* This class describes an URL */

#include <assert.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <sys/types.h>
#include <sys/socket.h>

#include "options.h"

#include "types.h"
#include "global.h"
#include "utils/url.h"
#include "utils/text.h"
#include "utils/connexion.h"
#include "utils/debug.h"

#ifdef FENS_OPT_COOKIES
#define initCookie() cookie=NULL
#else // FENS_OPT_COOKIES
#define initCookie() ((void) 0)
#endif // FENS_OPT_COOKIES
/* small functions used later */
static uint siteHashCode(char *host)
{
    uint h = 0;
    uint i = 0;
    while (host[i] != 0) {
        h = 37* h + host[i];
        i++;
    }
    return h % FENS_NAMEDSITE_LIST_SIZE;
}

/* return the int with correspond to a char
 * -1 if not an hexa char */
static int int_of_hexa(char c)
{
    if (c >= '0' && c <= '9')
        return (c - '0');
    else if (c >= 'a' && c <= 'f')
        return (c - 'a' + 10);
    else if (c >= 'A' && c <= 'F')
        return (c - 'A' + 10);
    else
        return -1;
}

/* normalize a file name : also called by robots.txt parser
 * return true if it is ok, false otherwise (cgi-bin)
 */
bool fileNormalize(char *file)
{
    int i = 0;
    while (file[i] != 0 && file[i] != '#') {
        if (file[i] == '/') {
            if (file[i + 1] == '.' && file[i + 2] == '/') {
                // suppress /./
                int j = i + 3;
                while (file[j] != 0) {
                    file[j - 2] = file[j];
                    j++;
                }
                file[j - 2] = 0;
            } else if (file[i + 1] == '/') {
                // replace // by /
                int j = i + 2;
                while (file[j] != 0) {
                    file[j - 1] = file[j];
                    j++;
                }
                file[j - 1] = 0;
            } else if (file[i + 1] == '.' && file[i + 2] == '.' && file[i + 3]
                    == '/') {
                // suppress /../
                if (i == 0) {
                    // the file name starts with /../ : error
                    return false;
                } else {
                    int j = i + 4, dec;
                    i--;
                    while (file[i] != '/') {
                        i--;
                    }
                    dec = i + 1 - j; // dec < 0
                    while (file[j] != 0) {
                        file[j + dec] = file[j];
                        j++;
                    }
                    file[j + dec] = 0;
                }
            } else if (file[i + 1] == '.' && file[i + 2] == 0) {
                // suppress /.
                file[i + 1] = 0;
                return true;
            } else if (file[i + 1] == '.' && file[i + 2] == '.' && file[i + 3]
                    == 0) {
                // suppress /..
                if (i == 0) {
                    // the file name starts with /.. : error
                    return false;
                } else {
                    i--;
                    while (file[i] != '/') {
                        i--;
                    }
                    file[i + 1] = 0;
                    return true;
                }
            } else { // nothing special, go forward
                i++;
            }
        } else if (file[i] == '%') {
            int v1 = int_of_hexa(file[i + 1]);
            int v2 = int_of_hexa(file[i + 2]);
            if (v1 < 0 || v2 < 0)
                return false;
            char c = 16 * v1 + v2;
            if (isgraph(c)) {
                file[i] = c;
                int j = i + 3;
                while (file[j] != 0) {
                    file[j - 2] = file[j];
                    j++;
                }
                file[j - 2] = 0;
                i++;
            } else if (c == ' ' || c == '/') { // keep it with the % notation
                i += 3;
            } else { // bad url
                return false;
            }
        } else { // nothing special, go forward
            i++;
        }
    }
    file[i] = 0;
    return true;
}

/**************************************/
/* definition of methods of class url */
/**************************************/

/* Constructor : Parses an url */
url::url(char *u, int8_t depth, url *base)
{
    newUrl();
#ifdef FENS_OPT_URL_SITEID
    siteId = 0;
#endif // FENS_OPT_URL_SITEID
    this->depth = depth;
    host = NULL;
    port = 80;
    security = false;
    redirFrom = NULL;
    redirFromHost = NULL;
    file = NULL;
    initCookie();
#ifdef FENS_OPT_URL_TAGS
    tag = 0;
#endif // FENS_OPT_URL_TAGS
    if (startWith("http://", u)) {
        // absolute url
        parse(u + 7);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete[] file;
            file = NULL;
            delete[] host;
            host = NULL;
        }
    } else if (startWith("https://", u)) {
        security = true;
        port = 443;
        // absolute url
        parse(u + 8);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete[] file;
            file = NULL;
            delete[] host;
            host = NULL;
        }
    } else if (base != NULL) {
        if (startWith("http:", u)) {
            parseWithBase(u + 5, base);
        } else if (isProtocol(u)) {
            // Unknown protocol (mailto, ftp, news, file, gopher...)
        } else {
            parseWithBase(u, base);
        }
    }
}

#ifdef FENS_OPT_URL_SITEID
/* for fens */
url::url (uint siteId, char *u, int8_t depth, url *base) {
    newUrl();
    this->siteId = siteId;
    this->depth = depth;
    host = NULL;
    port = 80;
    security = false;
    redirFrom = NULL;
    redirFromHost = NULL;
    file = NULL;
    initCookie();
#ifdef FENS_OPT_URL_TAGS
    tag = 0;
#endif // FENS_OPT_URL_TAGS
    if (startWith("http://", u)) {
        // absolute url
        parse (u + 7);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete [] file;
            file = NULL;
            delete [] host;
            host = NULL;
        }
    } else if (startWith("https://", u)) {
        security = true;
        port = 443;
        // absolute url
        parse (u + 8);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete [] file;
            file = NULL;
            delete [] host;
            host = NULL;
        }
    } else if (base != NULL) {
        if (startWith("http:", u)) {
            parseWithBase(u+5, base);
        } else if (isProtocol(u)) {
            // Unknown protocol (mailto, ftp, news, file, gopher...)
        } else {
            parseWithBase(u, base);
        }
    }
}
#endif // FENS_OPT_URL_SITEID
/* constructor used by input */
url::url(char *line, int8_t depth)
{
    newUrl();
#ifdef FENS_OPT_URL_SITEID
    siteId = 0;
#endif // FENS_OPT_URL_SITEID
    this->depth = depth;
    host = NULL;
    port = 80;
    security = false;
    redirFrom = NULL;
    redirFromHost = NULL;
    file = NULL;
    initCookie();
    int i = 0;
#ifdef FENS_OPT_URL_TAGS
    tag = 0;
    while (line[i] >= '0' && line[i] <= '9') {
        tag = 10*tag + line[i] - '0';
        i++;
    }
    i++;
#endif // FENS_OPT_URL_TAGS
    if (startWith("http://", line + i)) {
        parse(line + i + 7);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete[] file;
            file = NULL;
            delete[] host;
            host = NULL;
        }
    } else if (startWith("https://", line + i)) {
        security = true;
        port = 443;
        parse(line + i + 8);
        // normalize file name
        if (file != NULL && !normalize(file)) {
            delete[] file;
            file = NULL;
            delete[] host;
            host = NULL;
        }
    }
}

/* Constructor : read the url from a file (cf serialize)
 */
url::url(char *line)
{
    newUrl();
    int i = 0;
    // Read depth
    depth = 0;
    while (line[i] >= '0' && line[i] <= '9') {
        depth = 10* depth + line[i] - '0';
        i++;
    }
#ifdef FENS_OPT_URL_TAGS
    // read tag
    tag = 0; i++;
    while (line[i] >= '0' && line[i] <= '9') {
        tag = 10*tag + line[i] - '0';
        i++;
    }
#endif // FENS_OPT_URL_TAGS
#ifdef FENS_OPT_URL_SITEID
    // read site id
    siteId = 0; i++;
    while (line[i] >= '0' && line[i] <= '9') {
        siteId = 10*siteId + line[i] - '0';
        i++;
    }

#endif // FENS_OPT_URL_SITEID
    // read security
    security = ('1' == line[++i]);
    // this is a blank
    i++;

    int deb = ++i;
    // Read host
    while (line[i] != ':') {
        i++;
    }
    line[i] = 0;
    host = newString(line + deb);
    i++;
    // Read port
    port = 0;
    while (line[i] >= '0' && line[i] <= '9') {
        port = 10* port + line[i] - '0';
        i++;
    }
    // Read file name
    deb = i;
    while (line[i] != ' ') {
        i++;
    }
    if (i == deb) {
        file = new char[1];
        file[0] = 0;
    } else {
        line[i] = 0;
        file = newString(line + deb);
    }
    // Read redirFrom
    deb = ++i;
    while (line[i] && line[i] != ' ') {
        i++;
    }
    if (line[i] == ' ') {
        line[i] = 0;
        i++;
    }
    if (line[deb] == '0') {
        redirFrom = NULL;
        redirFromHost = NULL;
    } else {
        redirFrom = newString(line + deb);
        redirFromHost = giveHost(redirFrom);
    }

#ifdef FENS_OPT_COOKIES
    char *cpos = strchr(line+i, ' ');
    if (cpos == NULL) {
        cookie = NULL;
    } else {
        *cpos = 0;
        // read cookies
        cookie = new char[FENS_MAX_COOKIE_SIZE];
        strcpy(cookie, cpos+1);
    }
#endif // FENS_OPT_COOKIES
}

/* constructor used by giveBase */
url::url(char *host, uint port, char *file)
{
    newUrl();
    initCookie();
    this->host = host;
    this->port = port;
    this->security = false;
    this->redirFrom = NULL;
    this->redirFromHost = NULL;
    this->file = file;
}

/* Destructor */
url::~url()
{
    delUrl();
    delete[] host;
    delete[] redirFrom;
    delete[] redirFromHost;
    delete[] file;
#ifdef FENS_OPT_COOKIES
    delete [] cookie;
#endif // FENS_OPT_COOKIES
}

/* Is it a valid url ? */
bool url::isValid()
{
    if (host == NULL)
        return false;
    int lh = strlen(host);
    return file != NULL && lh < FENS_MAX_SITENAME_SIZE && lh + strlen(file) + 18
            < FENS_MAX_URL_SIZE;
}

/* print an URL */
void url::print()
{
    if (security) {
        if (443 == port) {
            printf("https://%s%s\n", host, file);
        } else {
            printf("https://%s:%u%s\n", host, port, file);
        }
    } else {
        if (80 == port) {
            printf("http://%s%s\n", host, file);
        } else {
            printf("http://%s:%u%s\n", host, port, file);
        }
    }
}

void url::setRedirFrom(char *from)
{
    delete[] redirFrom;
    redirFrom = newString(from);
    delete[] redirFromHost;
    redirFromHost = giveHost(redirFrom);
}

/* Set depth to max if necessary
 * try to find the ip addr
 * answer false if forbidden by robots.txt, true otherwise */
bool url::initOK(url *from)
{
#if defined(FENS_OPT_DEPTHBYSITE) || defined(FENS_OPT_COOKIES)
    if (strcmp(from->getHost(), host)) { // different site
#ifdef FENS_OPT_DEPTHBYSITE
        depth = global::depthInSite;
#endif // FENS_OPT_DEPTHBYSITE
    } else { // same site
#ifdef FENS_OPT_COOKIES
        if (from->cookie != NULL) {
            cookie = new char[FENS_MAX_COOKIE_SIZE];
            strcpy(cookie, from->cookie);
        }
#endif // FENS_OPT_COOKIES
    }
#endif // defined(FENS_OPT_DEPTHBYSITE) || defined(FENS_OPT_COOKIES)
    if (depth < 0) {
        errno = tooDeep;
        return false;
    }
    NamedSite *ns = global::namedSiteList + (hostHashCode());
    if (!strcmp(ns->name, host) && ns->port == port) {
        switch (ns->dnsState) {
            case errorDns:
                errno = fastNoDns;
                return false;
            case noConnDns:
                errno = fastNoConn;
                return false;
            case doneDns:
                if (!ns->testRobots(file)) {
                    errno = fastRobots;
                    return false;
                }
        }
    }
    return true;
}

/* return the base of the url */
url *url::giveBase()
{
    int i = strlen(file);
    assert (file[0] == '/');
    while (file[i] != '/') {
        i--;
    }
    char *newFile = new char[i + 2];
    memcpy(newFile, file, i + 1);
    newFile[i + 1] = 0;
    return new url(newString(host), port, newFile);
}

/** return a char * representation of the url
 * give means that you have to delete the string yourself
 */
char *url::giveUrl()
{
    char *tmp;
    int i = strlen(file);
    int j = strlen(host);

    if (security) {
        tmp = new char[19 + i + j]; // 8 + j + 1 + 9 + i + 1
        // https://(host):(port)(file)\0
        strcpy(tmp, "https://");
        strcpy(tmp + 8, host);
        j += 8;

        if (port != 443) {
            j += sprintf(tmp + j, ":%u", port);
        }
    } else {
        tmp = new char[18 + i + j]; // 7 + j + 1 + 9 + i + 1
        // http://(host):(port)(file)\0
        strcpy(tmp, "http://");
        strcpy(tmp + 7, host);
        j += 7;

        if (port != 80) {
            j += sprintf(tmp + j, ":%u", port);
        }
    }

    // Copy file name
    while (i >= 0) {
        tmp[j + i] = file[i];
        i--;
    }
    return tmp;
}

/** write the url in a buffer
 * buf must be at least of size FENS_MAX_URL_SIZE
 * returns the size of what has been written (not including '\0')
 */
int url::writeUrl(char *buf)
{
    if (security) {
        if (443 == port)
            return sprintf(buf, "https://%s%s", host, file);
        else
            return sprintf(buf, "https://%s:%u%s", host, port, file);
    } else {
        if (80 == port)
            return sprintf(buf, "http://%s%s", host, file);
        else
            return sprintf(buf, "http://%s:%u%s", host, port, file);
    }
}

/* serialize the url for the Persistent Fifo */
char *url::serialize()
{
    // this buffer is protected by the lock of PersFifo
    static char statstr[FENS_MAX_URL_SIZE + 40 + 16 + FENS_MAX_URL_SIZE + FENS_MAX_COOKIE_SIZE];
    int pos = sprintf(statstr, "%u ", depth);
#ifdef FENS_OPT_URL_TAGS
    pos += sprintf(statstr+pos, "%u ", tag);
#endif // FENS_OPT_URL_TAGS
#ifdef FENS_OPT_URL_SITEID
    // site id
    pos += sprintf(statstr+pos, "%u ", siteId);
#endif // FENS_OPT_URL_SITEID
    // is SSL url, security = 1
    pos += sprintf(statstr + pos, "%u ", security);
    pos += sprintf(statstr + pos, "%s:%u%s", host, port, file);
    //redirect from
    if (redirFrom == NULL) {
        pos += sprintf(statstr + pos, " 0");
    } else {
        pos += sprintf(statstr + pos, " %s", redirFrom);
    }
#ifdef FENS_OPT_COOKIES
    if (cookie != NULL) {
        pos += sprintf(statstr+pos, " %s", cookie);
    }
#endif // FENS_OPT_COOKIES
    statstr[pos] = '\n';
    statstr[pos + 1] = 0;
    return statstr;
}

/* very thread unsafe serialisation in a static buffer */
char *url::getUrl()
{
    static char statstr[FENS_MAX_URL_SIZE + 40];
    if (security) {
        sprintf(statstr, "https://%s:%u%s", host, port, file);
    } else {
        sprintf(statstr, "http://%s:%u%s", host, port, file);
    }
    return statstr;
}

/* return a hashcode for the host of this url */
uint url::hostHashCode()
{
    return siteHashCode(host);
}

/* return a hashcode for this url */
uint url::hashCode()
{
    unsigned int h = port;
    unsigned int i = 0;
    while (host[i] != 0) {
        h = 31* h + host[i];
        i++;
    }
    i = 0;
    while (file[i] != 0) {
        h = 31* h + file[i];
        i++;
    }
    return h % FENS_HASH_SIZE;
}

char* url::giveHost(char* u)
{
    int offset = 0;
    if (startWith("http://", u)) {
        offset = 7;
    } else if (startWith("https://", u)) {
        offset = 8;
    } else {
        return NULL;
    }

    int fin = 0;
    // Find the end of host name (put it into lowerCase)
    while (u[fin + offset] != '/' && u[fin + offset] != ':' && u[fin + offset]
            != 0) {
        fin++;
    }
    if (fin == 0)
        return NULL;

    // get host name
    char* h = new char[fin + 1];
    for (int i = 0; i < fin; i++) {
        h[i] = lowerCase(u[i + offset]);
    }
    h[fin] = 0;

    return h;
}

bool url::isSameHost(char* url)
{
    bool same = false;
    char* h1 = NULL;
    char* h2 = giveHost(url);
    if (redirFrom) {
        h1 = this->redirFromHost;
    } else {
        h1 = this->getHost();
    }

    same = !strcmp(h2, h1);
    delete[] h2;

    return same;
}

bool url::isSameHost(url* u)
{
    bool same = false;
    char* h1 = NULL;
    char* h2 = u->getHost();
    if (redirFrom) {
        h1 = this->redirFromHost;
    } else {
        h1 = this->getHost();
    }

    same = !strcmp(h2, h1);

    return same;
}

/* parses a url :
 * at the end, arg must have its initial state,
 * http:// has allready been suppressed
 */
void url::parse(char *arg)
{
    int deb = 0, fin = deb;
    // Find the end of host name (put it into lowerCase)
    while (arg[fin] != '/' && arg[fin] != ':' && arg[fin] != 0) {
        fin++;
    }
    if (fin == 0)
        return;

    // get host name
    host = new char[fin + 1];
    for (int i = 0; i < fin; i++) {
        host[i] = lowerCase(arg[i]);
    }
    host[fin] = 0;

    // get port number
    if (arg[fin] == ':') {
        port = 0;
        fin++;
        while (arg[fin] >= '0' && arg[fin] <= '9') {
            port = port * 10 + arg[fin] - '0';
            fin++;
        }
    }

    // get file name
    if (arg[fin] != '/') {
        // www.inria.fr => add the final /
        file = newString("/");
    } else {
        file = newString(arg + fin);
    }
}

/** parse a file with base
 */
void url::parseWithBase(char *u, url *base)
{
    // cat filebase and file
    if (u[0] == '/') {
        file = newString(u);
    } else {
        uint lenb = strlen(base->file);
        char *tmp = new char[lenb + strlen(u) + 1];
        memcpy(tmp, base->file, lenb);
        strcpy(tmp + lenb, u);
        file = tmp;
    }
    if (!normalize(file)) {
        delete[] file;
        file = NULL;
        return;
    }
    host = newString(base->host);
    port = base->port;
}

/** normalize file name
 * return true if it is ok, false otherwise (cgi-bin)
 */
bool url::normalize(char *file)
{
    return fileNormalize(file);
}

/* Does this url starts with a protocol name */
bool url::isProtocol(char *s)
{
    uint i = 0;
    while (isalnum(s[i])) {
        i++;
    }
    return s[i] == ':';
}

#ifdef FENS_OPT_COOKIES
#define addToCookie(s) len = strlen(cookie); \
    strncpy(cookie+len, s, FENS_MAX_COOKIE_SIZE-len); \
    cookie[FENS_MAX_COOKIE_SIZE-1] = 0;

/* see if a header contain a new cookie */
void url::addCookie(char *header) {
    if (startWithIgnoreCase("set-cookie: ", header)) {
        char *pos = strchr(header+12, ';');
        if (pos != NULL) {
            int len;
            if (cookie == NULL) {
                cookie = new char[FENS_MAX_COOKIE_SIZE];
                cookie[0] = 0;
            } else {
                addToCookie("; ");
            }
            *pos = 0;
            addToCookie(header+12);
            *pos = ';';
        }
    }
}
#endif // FENS_OPT_COOKIES
