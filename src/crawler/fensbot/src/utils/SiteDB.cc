#include <sstream>

#include "utils/SiteDB.h"

using namespace fens;

SiteDB::SiteDB(const char* host, const char* user, const char* passwd,
        const char* db)
{
    _connection = new CMysql(host, user, passwd, db);
}

SiteDB::~SiteDB()
{
    if (_connection) {
        delete _connection;
    }
}

uint SiteDB::getSiteList(std::vector<MYSQL_ROW>& v)
{
    return getSiteList(v, 0, 0);
}

uint SiteDB::getSiteList(std::vector<MYSQL_ROW>& v, uint index, uint mod)
{
    uint num = 0;
    MYSQL_RES* rs = getSubSiteList(index, mod);
    while(rs != NULL) {
        MYSQL_ROW row;
        int len = 0;
        while((row = mysql_fetch_row(rs)) != NULL) {
            v.push_back(row);
            len++;
            num++;
        }
        _connection->free(rs);
        rs = NULL;
        if (len == FENS_MYSQL_MAX_RESULT_SIZE) {
            rs = getSubSiteList(index, mod);
        }
    }

    _connection->free(rs);

    return num;
}

MYSQL_RES* SiteDB::getSubSiteList(uint index, uint mod)
{
    std::stringstream ostr;
    ostr << "SELECT id,name,url,CONCAT(url,toppage) AS toppage FROM site WHERE status=0";
    if (index > 0 && mod > 0) {
        if (index < mod) {
            ostr << " AND MOD(id," << mod << ")=" << index;
        } else {
            ostr << " AND MOD(id," << mod << ")=" << 0;
        }
    }
    ostr << " LIMIT " << FENS_MYSQL_MAX_RESULT_SIZE << " FOR UPDATE";

    //printf("%s\n", ostr.str().c_str());

    //return NULL;

    _connection->query("START TRANSACTION");

    MYSQL_RES* rs = _connection->fetch(ostr.str().c_str());
    ostr.str("");
    if (rs) {
        MYSQL_ROW row;
        row = mysql_fetch_row(rs);
        if (row) {
            ostr << "UPDATE site SET status=1 WHERE status=0 AND id in (";
            ostr << row[0];
            while (row = mysql_fetch_row(rs)) {
                ostr << "," << row[0];
            }
            ostr << ")";

            _connection->query(ostr.str().c_str());
            _connection->query("COMMIT");

            mysql_data_seek(rs, 0);
        } else {
            _connection->query("ROLLBACK");
        }
    } else {
        _connection->query("ROLLBACK");
    }

    return rs;
}

bool SiteDB::updateSitePage(int siteId, int8_t depth, const char* url, const char* title, const char* body,
        const char* summary, time_t time)
{
    std::string str_url(url), str_title(title), str_body(body), str_summary(
            summary);
    return updateSitePage(siteId, depth, str_url, str_title, str_body, str_summary,
            time);
}

size_t SiteDB::getUtf8ByteNumForWord(const char& firstCh)
{
    unsigned char temp = 0x80;
    size_t num = 0;

    while (temp & firstCh) {
        num++;
        temp = (temp >> 1);
    }
    return num;
}

std::string SiteDB::getTopString(const std::string &s, size_t SUBLEN)
{
    std::string top;
    size_t len = s.length();
    if (len <= SUBLEN) {
        top.assign(s);
    } else {
        size_t clen = 0, i = 0, c1 = 0, c2 = 0, num = 0;
        while (i < len && clen < SUBLEN) {
            //printf("%d\n",(int)s[i]);
            num = getUtf8ByteNumForWord(s[i]);
            if (num > 0) {
                c2++;
                i += num;
            } else {
                c1++;
                i++;
            }

            clen++;
        }
        //printf("%d\t%d\t%d\n", c1,c2,c1+c2*2);
        top.assign(s.substr(0, i)).append("…");
    }

    return top;
}

bool SiteDB::updateSitePage(int siteId, int8_t depth, const std::string &url, const std::string &title,
        const std::string &body, const std::string &summary, time_t time)
{
    //fixed for japanese word
    //std::string summary = body.substr(0, 200);
    size_t SUBLEN = 200;
    std::string subsummary = getTopString(summary, SUBLEN);

    std::ostringstream os;
    /*
     * REPLACE INTO vs INSERT INTO ON DEPULICATE KEY
     * REPLACE INTO: insert if not exists, else remove and replace
     *   auto_increment++, and other field filled by default.
     * INSERT INTO ON DEPULICATE KEY: insert if not exists, else update the value.
     *
     */
    os
            << "REPLACE INTO site_page(site_id,url,title,body,summary,depth,create_time) VALUES("
            << siteId << ",'" << _connection->escapeString(url) << "','"
            << _connection->escapeString(title) << "','" << _connection->escapeString(body)
            << "','" << _connection->escapeString(subsummary) << "',"
            << (uint)depth << "," << time << ")";

    return _connection->query(os.str().c_str());
}

bool SiteDB::deleteSiteAllPage(int siteId)
{
    std::ostringstream os;
    os << "DELETE FROM site_page WHERE site_id=" << siteId;

    return _connection->query(os.str().c_str());
}

