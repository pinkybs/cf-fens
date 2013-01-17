#include <iostream>
#include <cstring>
#include <cctype>
#include <sstream>

#include <map>

#include <unistd.h>

//htmlparser
#include <algorithm>
#include <ParserDom.h>
#include <utils.h>

#include "options.h"

#include "types.h"
#include "global.h"
#include "fetch/file.h"
#include "utils/text.h"
#include "utils/debug.h"
#include "interf/output.h"

#include "utils/SiteDB.h"
#include "utils/EncodingConverter.h"
//#include "utils/iconvert.cc"

using namespace htmlcxx;
using namespace std;

tree<HTML::Node>::iterator findTagNode(tree<HTML::Node>::iterator first, tree<
        HTML::Node>::iterator last, char* tagname)
{
    HTML::Node fTag;
    fTag.isTag(true);
    fTag.isComment(false);
    std::string sTagName(tagname);
    fTag.tagName(sTagName);

    return std::find(first, last, fTag);
}

bool isTagName(const char* tagName, const char* name)
{
    return !(strcasecmp(tagName, name));
}

std::string toLowerCase(const std::string& s)
{
    std::string lstr(s.length(), '\0');
    transform(s.begin(), s.end(), lstr.begin(), ::tolower);
    return lstr;
}

std::string toUpperCase(const std::string& s)
{
    std::string lstr(s.length(), '\0');
    transform(s.begin(), s.end(), lstr.begin(), ::toupper);
    return lstr;
}

std::string ltrim(const std::string& s, const std::string& drop = " ")
{
    std::string r;
    size_t found;
    found = s.find_first_not_of(drop);
    if (found != string::npos) {
        r = s.substr(found);
    }
    return r;
}

std::string rtrim(const std::string& s, const std::string& drop = " ")
{
    std::string r;
    size_t found;
    found = s.find_last_not_of(drop);
    if (found != string::npos) {
        r = s.substr(0, found + 1);
    }
    return r;
}

std::string trim(const std::string& s, const std::string& drop = " ")
{
    return ltrim(rtrim(s, drop), drop);
}

/*
 * parse Meta of "HTTP-EQUIV"
 * expires       : <META HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
 * Pragma        : <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
 * Refresh       : <META HTTP-EQUIV="Refresh" CONTENT="5;URL=http://www.yahoo.com">
 * Set-Cookie    : <META HTTP-EQUIV="Set-Cookie" CONTENT="cookievalue=xxx;
 *                       expires=Wednesday,21-Oct-98 16:14:21 GMT;path=/">
 * Window-target : <META HTTP-EQUIV="Window-target" CONTENT="_top">
 * Content-Type  : <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
 */
void parseHttpEquivMeta(std::map<std::string, std::string>& metas, tree<
        HTML::Node>::iterator begin, tree<HTML::Node>::iterator end)
{
    tree<HTML::Node>::iterator pIterator;
    for (pIterator = begin; pIterator != end; ++pIterator) {
        if (isTagName(pIterator->tagName().c_str(), "META")) { //<meta
            pIterator->parseAttributes();
            const std::pair<bool, std::string> attr = pIterator->attribute(
                    "http-equiv");
            if (attr.first) {
                const std::pair<bool, std::string> attr_content =
                        pIterator->attribute("content");
                if (attr_content.first) {
                    metas.insert(make_pair(toLowerCase(attr.second),
                            toLowerCase(attr_content.second)));
                } else {
                    metas.insert(make_pair(toLowerCase(attr.second),
                            std::string()));
                }
            }
        }
    }
}

/*
 * parse Meta of "NAME"
 * Keywords    : <META NAME="keywords" CONTENT="life, universe, mankind, plants,
 *                     relationships, the meaning of life, science">
 * description : <META NAME="description" CONTENT="This page is about the meaning
 *                     of life, the universe, mankind and plants.">
 * robots      : <META NAME="robots" CONTENT="none">
 *               all,none,index,noindex,follow,nofollow. default is "all"
 * author      : <META name="AUTHOR" content="somebody@example.com">
 */
void parseNameMeta(std::map<std::string, std::string>& metas, tree<
        HTML::Node>::iterator begin, tree<HTML::Node>::iterator end)
{
    tree<HTML::Node>::iterator pIterator;
    for (pIterator = begin; pIterator != end; ++pIterator) {
        if (isTagName(pIterator->tagName().c_str(), "META")) { //<meta
            pIterator->parseAttributes();
            const std::pair<bool, std::string> attr = pIterator->attribute(
                    "name");
            if (attr.first) {
                const std::pair<bool, std::string> attr_content =
                        pIterator->attribute("content");
                if (attr_content.first) {
                    metas.insert(make_pair(toLowerCase(attr.second),
                            HTML::single_blank(HTML::decode_entities(attr_content.second))));
                } else {
                    metas.insert(make_pair(toLowerCase(attr.second),
                            std::string()));
                }
            }
        }
    }
}

#define thisCharIs(i, c) (c == (i|32))

std::string getHeadContent(char* s, int l)
{
    std::string head;
    bool start = false, stop = false;
    int p1 = 0, p2 = 0;
    for (int i = 0; i < l; i++) {
        if (s[i] == '<') {
            if (thisCharIs(s[i+1], 'h') && thisCharIs(s[i+2], 'e')
                    && thisCharIs(s[i+3], 'a') && thisCharIs(s[i+4], 'd')) {
                start = true;
                p1 = i;
            }
            if (start && thisCharIs(s[i+1], '/') && thisCharIs(s[i+2], 'h')
                    && thisCharIs(s[i+3], 'e') && thisCharIs(s[i+4], 'a')
                    && thisCharIs(s[i+5], 'd')) {
                stop = true;
                p2 = i;
                break;
            }
        }
    }

    if (start && stop) {
        head = std::string(s + p1, p2 - p1 + 7);
    }

    return head;
}

/** A page has been loaded successfully, save it to database
 * @param page the page that has been fetched
 */
void loaded(html *page)
{
    //printf("========the page header content:=========\n");
    //printf("%s\n", page->getHeaders());
    //printf("%s\n", page->getPage());

    std::string curUrl;
    url* here = page->getUrl();
    char *tmp = here->getRedirFrom();
    if (tmp == NULL) {
        tmp = here->giveUrl();
        curUrl.assign(tmp);
        delete[] tmp;
    } else {
        curUrl.assign(tmp);
        //cout << "Redirect from " << curUrl << endl;
    }

    std::string hstr = getHeadContent(page->getPage(), page->getLength());
    if (hstr.length() == 0) {
        cout << "[1001] No head tag. [" << curUrl << "]" << endl;
        return;
    }

    HTML::ParserDom hparser;
    tree<HTML::Node> hdom = hparser.parseTree(hstr);
    // parse error or no content
    if (hdom.empty()) {
        cout << "[1000] Parsing error. Html head dom is empty. [" << curUrl
                << "]" << endl;
        return;
    }

    //get HttpEquiv meta
    std::map<std::string, std::string> metas;
    parseHttpEquivMeta(metas, hdom.begin(), hdom.end());

    //check meta if that has refresh
    //http://www.cctv.com/ ==>
    //<META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://www.cctv.com/default.shtml">
    std::map<std::string, std::string>::const_iterator i_refresh = metas.find(
            "refresh");
    if (i_refresh != metas.end()) {
        size_t found_refresh = i_refresh->second.find('=');
        if (found_refresh != std::string::npos) {
            //trim left and right whitespace and '
            //like: CONTENT="0;URL='https://secure.eiga.com/login/?r=http%3A%2F%2Feiga.com%2Fmypage%2F'"
            std::string newUrl = trim(i_refresh->second.substr(found_refresh
                    + 1), " '");
            cout << "[1101] Refresh new url: " << newUrl << ". [" << curUrl
                    << "]" << endl;
            url *nouv = new url(here->getSiteId(),
                    const_cast<char *> (newUrl.c_str()), here->getDepth(),
                    here->giveBase());
#ifdef FENS_OPT_URL_TAGS
            nouv->tag = here->tag;
#endif // FENS_OPT_URL_TAGS
            page->manageUrl(nouv, true);
            return;
        }
    }

    // get page charset
    std::string charset;
    char* ch = page->getHeaderCharset();
    if (ch[0] != '\0') {
        //fixed, convert all to lower case
        charset = toLowerCase(std::string(ch));
        //cout << "Charset=" << charset << endl;
    } else {
        //if html page HEADER do not give charset
        //try get from html head meta tag "Content-Type", like:
        //<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP" />
        std::map<std::string, std::string>::const_iterator hctype = metas.find(
                "content-type");
        if (hctype != metas.end()) {
            size_t hfound = hctype->second.find("charset=");
            if (hfound != std::string::npos) {
                charset.assign(hctype->second.substr(hfound + 8));
            }
        }
    }

    std::string htmlstr(page->getPage(), page->getLength());

    if (charset.length() > 0) {
        //cout << "[1011] Charset = " << charset << " . [" << curUrl << "]" << endl;
        //if not utf-8 | utf8
        //try to convert it to utf-8
        //strcasecmp(charset.c_str(), "utf-8") != 0
        //because we have converted charset to lower case
        //just use std::string comparison operators "!="
        //cout << "[Charset] " << charset << endl;
        if (charset != "utf-8" && charset != "utf8") {
            try {
                //fixed, some japanese charset is with "x-" prefix
                //eg. x-sjis, X-ISO2022JP
                //but iconv dont know these
                //so erase it
                if ((charset.length() > 2) && (charset[0] == 'x' && charset[1]
                        == '-')) {
                    charset.erase(0, 2);
                }
                //fixed, iconv dont know euc_jp
                //just use euc-jp
                if (charset == "euc_jp") {
                    charset = "euc-jp";
                }

                fens::EncodingConverter cter(charset, "utf-8");
                htmlstr = cter.convert(htmlstr);
            } catch (fens::EncodingConverter::Exception e) {
                cout << "[1010] Charset encoding converting error: "
                        << e.what() << ". [" << curUrl << "]" << endl;
                return;
            }
        }
    } else {
        //if charset is unknow
        //we should detect it
        //TODO:
        cout << "[1011] Charset is unknow. [" << curUrl << "]" << endl;
        return;
    }

    //cout << "The data [" << curUrl << "] will be saved in database." << endl;

    //cout << htmlstr << endl;
    //return;
    HTML::ParserDom parser;
    tree<HTML::Node> dom = parser.parseTree(htmlstr);

    // parse error or no content
    if (dom.empty()) {
        cout << "[1000] Parsing error. Html dom is empty. [" << curUrl << "]"
                << endl;
        return;
    }

    tree<HTML::Node>::iterator begin = dom.begin();
    tree<HTML::Node>::iterator end = dom.end();
    tree<HTML::Node>::iterator pIterator = NULL;
    tree<HTML::Node>::iterator pBegin = NULL;
    tree<HTML::Node>::iterator pEnd = NULL;

    //get html head node
    //if empty, return
    tree<HTML::Node>::iterator nHead = findTagNode(begin, end, "HEAD");
    if ((nHead == end) || (nHead.begin() == nHead.end())) {
        cout << "[1001] No head tag. [" << curUrl << "]" << endl;
        return;
    }

    pBegin = nHead.begin();
    pEnd = nHead.end();

    //get html title
    std::string title;
    tree<HTML::Node>::iterator nTitle = findTagNode(pBegin, pEnd, "TITLE");
    if ((nTitle != pEnd) && (nTitle.begin() != nTitle.end())) {
        pIterator = nTitle.begin();
        if (!pIterator->isTag() && !pIterator->isComment()) {
            //title.assign(pIterator->text());
            title
                    = HTML::single_blank(HTML::decode_entities(
                            pIterator->text()));
        }
    }

    // title is empty
    if (title.length() == 0) {
        cout << "[1002] Title is empty. [" << curUrl << "]" << endl;
        return;
    }

    //get Name meta
    std::map<std::string, std::string> nameMetas;
    parseNameMeta(nameMetas, nHead.begin(), nHead.end());
    //try get description
    std::map<std::string, std::string>::const_iterator metaDescription = nameMetas.find(
            "description");
    std::string description;
    if (metaDescription != nameMetas.end()) {
        description = metaDescription->second;
    }

    //get html body node
    //if empty, return
    tree<HTML::Node>::iterator nBody = findTagNode(begin, end, "BODY");
    if ((nBody == end) || (nBody.begin() == nBody.end())) {
        cout << "[1003] No body tag. [" << curUrl << "]" << endl;
        return;
    }

    std::ostringstream os;
    pBegin = nBody.begin();
    pEnd = nBody.end();
    const char* c_tagName;
    for (pIterator = pBegin; pIterator != pEnd; ++pIterator) {
        if (pIterator->isTag()) {
            // <script>, <noscript>, <style> content should be filtered.
            c_tagName = pIterator->tagName().c_str();
            if (isTagName(c_tagName, "SCRIPT") || isTagName(c_tagName,
                    "NOSCRIPT") || isTagName(c_tagName, "STYLE")) {
                //cout << c_tagName << endl;
                //skip_children();
                pIterator = pIterator.end();
                //fixed
                if (pIterator == pEnd) {
                    break;
                }
                continue;
            }
        }
        if ((!pIterator->isTag()) && (!pIterator->isComment())) {
            //cout << pIterator->text() << endl;
            os << pIterator->text() << ' ';
        }
    }

    std::string body = HTML::single_blank(HTML::decode_entities(os.str()));

    //cout << body << endl;
    //return;
    // body is empty
    if (body.length() == 0) {
        cout << "[1004] Body is empty. [" << curUrl << "]" << endl;
        return;
    }

    //  return;

    //cout << title << endl;
    //cout << body << endl;

    fens::SiteDB sitedb(global::mysqlConConfig.host,
            global::mysqlConConfig.user, global::mysqlConConfig.passwd,
            global::mysqlConConfig.db);

    //site_id, url, title, body, summary
    if (description.empty()) {
        //if description is empty, use body instead
        sitedb.updateSitePage(here->getSiteId(), here->getDepth(), curUrl, title, body, body, global::now);
    }
    else {
        sitedb.updateSitePage(here->getSiteId(), here->getDepth(), curUrl, title, body, description, global::now);
    }
}

/** The fetch failed
 * @param u the URL of the doc
 * @param reason reason of the fail
 */
void failure(url *u, FetchError reason)
{
    char *urlname = u->giveUrl();
    if (reason == err30X) {
        //cout << "[1100] Url redirect. [" << urlname << "]" << endl;
    } else {
        cout << "[1] FetchError: " << reason << ". [" << urlname << "]" << endl;
    }
    delete[] urlname;
}

/** initialisation function
 */
void initUserOutput()
{
    // do nothing
}

/** stats, called in particular by the webserver
 * the webserver is in another thread, so be careful
 * However, if it only reads things, it is probably not useful
 * to use mutex, because incoherence in the webserver is not as critical
 * as efficiency
 */
void outputStats(int fds)
{
    ecrire(fds, "Nothing to declare");
}
