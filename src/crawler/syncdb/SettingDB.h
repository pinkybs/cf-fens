#ifndef __FENS_SETTINGDB_H__
#define __FENS_SETTINGDB_H__

#include <cstdlib>
#include <ctime>

#include "CMysql.h"

namespace fens
{

    struct SyncSetting
    {
        size_t db_synchronize_timing;
        time_t last_synchronize_time;
        bool running;
        SyncSetting();
    };

    class SettingDB
    {
    public:
        SettingDB(const char* host, const char* user, const char* passwd,
                const char* db);
        ~SettingDB();
        SyncSetting getSetting();
        bool updateSyncTime();
        size_t beginSync(bool mode);
        bool endSync(size_t id);

    private:
        CMysql* _connection;

    };

}

#endif //__FENS_SETTINGDB_H__
