#include <sstream>

#include "SettingDB.h"

using namespace fens;

SyncSetting::SyncSetting()
{
    db_synchronize_timing = 0;
    last_synchronize_time = 0;
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

SyncSetting SettingDB::getSetting()
{
    SyncSetting setting;
    const char
            * sql =
                    "SELECT db_synchronize_timing,last_synchronize_time,synchronize_running FROM crawler_setting LIMIT 1";
    MYSQL_RES* rs = _connection->fetch(sql);
    if (rs) {
        MYSQL_ROW row;
        row = mysql_fetch_row(rs);
        if (row) {
            setting.db_synchronize_timing = (size_t) strtoul(row[0], NULL, 0);
            setting.last_synchronize_time = (time_t) strtoul(row[1], NULL, 0);
            setting.running = (bool) atoi(row[2]);
        }
    }

    return setting;
}

bool SettingDB::updateSyncTime()
{
    time_t now = time(NULL);
    std::ostringstream os;
    os << "UPDATE crawler_setting SET last_synchronize_time=" << now;

    return _connection->query(os.str().c_str());
}

size_t SettingDB::beginSync(bool mode)
{
    // update sync status to running
    _connection->query("UPDATE crawler_setting SET synchronize_running=1");

    time_t now = time(NULL);
    std::ostringstream os;

    // get the name of boolvalue: os << boolalpha << boolvalue
    os << "INSERT INTO db_synchronize_log(mode, begin_time) VALUES(" << mode
            << "," << now << ")";

    bool status = _connection->query(os.str().c_str());

    if (status) {
        return _connection->lastInsertId();
    } else {
        return -1;
    }
}

bool SettingDB::endSync(size_t id)
{
    // update sync status to not running
    _connection->query("UPDATE crawler_setting SET synchronize_running=0");

    time_t now = time(NULL);
    std::ostringstream os;
    os << "UPDATE db_synchronize_log SET end_time=" << now
            << ", is_ended=1 WHERE id=" << id;

    return _connection->query(os.str().c_str());
}

