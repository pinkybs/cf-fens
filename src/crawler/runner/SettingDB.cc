#include <sstream>

#include "SettingDB.h"

using namespace fens;

CrawlerSetting::CrawlerSetting()
{
    number = 0;
    timing = 0;
    last_crawling_time = 0;
}

SettingDB::SettingDB(const char* host, const char* user, const char* passwd,
        const char* db)
{
    _connection = new CMysql(host, user, passwd, db);
}

SettingDB::~SettingDB()
{
    if (_connection) {
        delete _connection;
    }
}

CrawlerSetting SettingDB::getSetting()
{
    CrawlerSetting setting;
    const char* sql =
            "SELECT number,timing,new_timing,last_new_crawling_time,last_crawling_time,synchronize_running FROM crawler_setting LIMIT 1";
    MYSQL_RES* rs = _connection->fetch(sql);
    if (rs) {
        MYSQL_ROW row;
        row = mysql_fetch_row(rs);
        if (row) {
            setting.number = (size_t) strtoul(row[0], NULL, 0);
            setting.timing = (size_t) strtoul(row[1], NULL, 0);
            setting.new_timing = (size_t) strtoul(row[2], NULL, 0);
            setting.last_new_crawling_time = (time_t) strtoul(row[3], NULL, 0);
            setting.last_crawling_time = (time_t) strtoul(row[4], NULL, 0);
            setting.synchronize_running = (bool)atoi(row[5]);
        }
    }

    return setting;
}

bool SettingDB::updateCrawlingTime()
{
    time_t now = time(NULL);
    return updateCrawlingTime(now);
}

bool SettingDB::updateCrawlingTime(time_t t)
{
    std::ostringstream os;
    os << "UPDATE crawler_setting SET last_crawling_time=" << t;

    return _connection->query(os.str().c_str());
}

bool SettingDB::updateNewCrawlingTime()
{
    time_t now = time(NULL);
    return updateNewCrawlingTime(now);
}

bool SettingDB::updateNewCrawlingTime(time_t t)
{
    std::ostringstream os;
    os << "UPDATE crawler_setting SET last_new_crawling_time=" << t;

    return _connection->query(os.str().c_str());
}

bool SettingDB::updateSiteForNewCrawling()
{
    return updateSiteStatus(false);
}

bool SettingDB::updateSiteStatus(bool n)
{
    int status = n ? 1 : 0;
    std::ostringstream os;
    os << "UPDATE site SET status=" << status;

    return _connection->query(os.str().c_str());
}
