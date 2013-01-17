#ifndef __FENS_SITEDB_H__
#define __FENS_SITEDB_H__

#include <string>
#include <vector>
#include <ctime>

#include "types.h"
#include "utils/Vector.h"
#include "utils/CMysql.h"

namespace fens
{
    class SiteDB
    {
    public:
        SiteDB(const char* host, const char* user, const char* passwd,
                const char* db);
        ~SiteDB();
        uint getSiteList(std::vector<MYSQL_ROW>& v);
        uint getSiteList(std::vector<MYSQL_ROW>& v, uint index, uint mod);
        bool updateSitePage(int siteId, int8_t depth, const char* url, const char* title, const char* body,
                const char* summary, time_t time);
        bool updateSitePage(int siteId, int8_t depth, const std::string &url, const std::string &title,
                const std::string &body, const std::string &summary, time_t time);
        bool deleteSiteAllPage(int siteId);

    private:
        MYSQL_RES* getSubSiteList(uint index, uint mod);
        size_t getUtf8ByteNumForWord(const char& firstCh);
        std::string getTopString(const std::string &s, size_t SUBLEN);
        CMysql* _connection;

    };

}

#endif //__FENS_SITEDB_H__
