#ifndef __FENS_SETTINGDB_H__
#define __FENS_SETTINGDB_H__

#include <cstdlib>
#include <ctime>

#include "CMysql.h"

namespace fens
{

    struct CrawlerSetting
    {
        size_t number;
        size_t timing;
        size_t new_timing;
        time_t last_new_crawling_time;
        time_t last_crawling_time;
        bool synchronize_running;
        CrawlerSetting();
    };

    class SettingDB
    {
    public:
        SettingDB(const char* host, const char* user, const char* passwd,
                const char* db);
        ~SettingDB();
        CrawlerSetting getSetting();
        bool updateCrawlingTime();
        bool updateCrawlingTime(time_t t);
        bool updateNewCrawlingTime();
        bool updateNewCrawlingTime(time_t t);
        bool updateSiteForNewCrawling();
        bool updateSiteStatus(bool n);

    private:
        CMysql* _connection;

    };

}

#endif //__FENS_SETTINGDB_H__
