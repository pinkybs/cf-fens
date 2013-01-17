#include <cstdio>
#include <unistd.h>
#include <errno.h>
#include <signal.h>

#include <fstream>
#include <sstream>

#define SI_SUPPORT_IOSTREAMS
#include "SimpleIni.h"
#include "SettingDB.h"

static void pauseCrawler();
static void continueCrawler();

int main(int argc, char *argv[])
{
    bool mode = false;
    char *configFile = "config.ini";
    int pos = 1;
    while (pos < argc) {
        if (!strcmp(argv[pos], "-a")) {
            mode = true;
            pos++;
        } else if (!strcmp(argv[pos], "-c") && argc > pos + 1) {
            configFile = argv[pos + 1];
            pos += 2;
        } else {
            pos++;
        }
    }

    // load from a data file
    CSimpleIniA ini(true, false, false);
    SI_Error rc = ini.LoadFile(configFile);

    if (rc < 0) {
        printf("Load config.ini failure!\n");
        return -1;
    }

    const char* host = ini.GetValue("mysql", "host", NULL);
    const char* user = ini.GetValue("mysql", "user", NULL);
    const char* passwd = ini.GetValue("mysql", "passwd", NULL);
    const char* db = ini.GetValue("mysql", "db", NULL);

    fens::SettingDB settingdb(host, user, passwd, db);
    fens::SyncSetting setting = settingdb.getSetting();

    if (setting.running) {
        printf("Synchronize is already running!!!\n");
        return 1;
    }

    time_t now = time(NULL);
    time_t next_do_time = setting.last_synchronize_time
            + setting.db_synchronize_timing * 3600;
    if (!mode && now < next_do_time) {
        printf("Synchronize time has not arrived yet, do nothing!\n");
        printf("Now is %s", ctime(&now));
        printf("Next time to do is %s", ctime(&next_do_time));
        return 1;
    }

    // get mysqldump path
    const char* mysqldump = ini.GetValue("mysqldump", "path", NULL);
    if (!mysqldump) {
        printf("mysqldump path can not be empty!\n");
        return 1;
    }
    // get mysqldump options
    const char* dumpopts = ini.GetValue("mysqldump", "options", NULL);
    // get mysqldump tables
    const char* tables = ini.GetValue("mysqldump", "tables", NULL);
    if (!tables) {
        printf("mysqldump tables can not be empty!\n");
        return 1;
    }

    //get mysql path
    const char* mysql = ini.GetValue("mysqlclient", "path", NULL);
    if (!mysql) {
        printf("mysql path can not be empty!\n");
        return 1;
    }
    // get mysql options
    const char* mysqlopts = ini.GetValue("mysqlclient", "options", NULL);
    const char* clienthost = ini.GetValue("mysqlclient", "host", NULL);
    const char* clientuser = ini.GetValue("mysqlclient", "user", NULL);
    const char* clientpasswd = ini.GetValue("mysqlclient", "passwd", NULL);
    const char* clientdb = ini.GetValue("mysqlclient", "db", NULL);

    //update Synchronize time
    settingdb.updateSyncTime();

    //begin Synchronize
    //update begin time
    size_t id = settingdb.beginSync(mode);
    if (id <= 0) {
        printf("update synchronize log begin time error!\n");
        return 1;
    }

    //run mysqldump
    std::ostringstream cmd;
    cmd << mysqldump << " " << dumpopts << " -h" << host << " -u" << user
            << " -p" << passwd << " " << db << " " << tables << " | " << mysql
            << " " << mysqlopts << " -h" << clienthost << " -u" << clientuser
            << " -p" << clientpasswd << " " << clientdb;

    //printf("%s", cmd.str().c_str());

    //pause crawler
    pauseCrawler();

    int status = system(cmd.str().c_str());

    if (status < 0) {
        printf("system execute error!\n");
    } else {
        printf("Sync db successful!\n");
    }

    //continue crawler
    continueCrawler();

    //end
    //update end time
    settingdb.endSync(id);

}

static void pauseCrawler()
{
    // send singal SIGSTOP (linux is 19)
    system("ps -efww|grep fensbot|grep -v grep|awk '{print $2}'|xargs kill -19");
    sleep(2);
}

static void continueCrawler()
{
    // send singal SIGCONT (linux is 18)
    system("ps -efww|grep fensbot|grep -v grep|awk '{print $2}'|xargs kill -18");
}
