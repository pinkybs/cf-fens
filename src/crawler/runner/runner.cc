#include <cstdio>
#include <unistd.h>
#include <errno.h>
#include <signal.h>

#include <fstream>

#define SI_SUPPORT_IOSTREAMS
#include "SimpleIni.h"
#include "SettingDB.h"

static void stopCrawler();
static void pauseCrawler();

int main()
{
    // load from a data file
    CSimpleIniA ini(true, false, false);
    SI_Error rc = ini.LoadFile("config.ini");

    if (rc < 0) {
        printf("Load config.ini failure!\n");
        return -1;
    }

    const char* host = ini.GetValue("mysql", "host", NULL);
    const char* user = ini.GetValue("mysql", "user", NULL);
    const char* passwd = ini.GetValue("mysql", "passwd", NULL);
    const char* db = ini.GetValue("mysql", "db", NULL);

    fens::SettingDB settingdb(host, user, passwd, db);
    fens::CrawlerSetting setting = settingdb.getSetting();

    if (setting.synchronize_running) {
        printf("Database Synchronization(Replication) is running!!!\nPlease start crawling some time later.\n");
        return 1;
    }

    struct tm* timeinfo = localtime(&(setting.last_crawling_time));

    printf("************ Crawler Setting Configure ****************\n");
    printf("* number = %d\n", setting.number);
    printf("* timing = %d (hours)\n", setting.timing);
    printf("* new_timing = %d (hours)\n", setting.new_timing);
    printf("* last_crawling_time = %d-%02d-%02d %02d:%02d:%02d (%d)\n", 1900
            + timeinfo->tm_year, 1 + timeinfo->tm_mon, timeinfo->tm_mday,
            timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec,
            setting.last_crawling_time);
    printf("*******************************************************\n");

    if (setting.number == 0) {
        printf("Crawler Number=0, do nothing!\n");
        return 1;
    }

    time_t now = time(NULL);
    // when we should do crawling
    time_t next_do_time = setting.last_crawling_time + setting.timing * 3600;
    // when we should do new crawling
    time_t next_new_do_time = setting.last_new_crawling_time + setting.new_timing * 3600 * 24;

    // is new crawling for all sites
    bool isNewCrawling = (now >= next_new_do_time);

    // is crawling
    bool isCrawling = (now >= next_do_time);

    if (!isCrawling && !isNewCrawling) {
        printf("Crawling time has not arrived yet, do nothing!\n");
        printf("Now is %s", ctime(&now));
        printf("Next time to do is %s", ctime(&next_do_time));
        printf("Next new time to do is %s", ctime(&next_new_do_time));
        return 1;
    }

    // get fensbot path
    const char* fensbot = ini.GetValue("fensbot", "path", NULL);
    if (!fensbot) {
        printf("fensbot path can not be empty!\n");
        return 1;
    }
    // get fensbot config file path
    const char* fensbotconfig = ini.GetValue("fensbot", "config", NULL);
    if (!fensbotconfig) {
        printf("fensbot config file path can not be empty!\n");
        return 1;
    }

    //update crawling time
    settingdb.updateCrawlingTime(now);

    if (isNewCrawling) {
        settingdb.updateSiteForNewCrawling();
        settingdb.updateNewCrawlingTime(now);
    }

    int pid;
    char si[10];
    char nodump[20];
    char maxTimeLimit[20];
    sprintf(maxTimeLimit, "%d", setting.timing * 3600);
    signal(SIGPIPE, SIG_IGN);
    for (int i = 0; i < setting.number; i++) {
        sprintf(si, "%d/%d", i + 1, setting.number);
        sprintf(nodump, "nodump_%d", i + 1);
        pid = vfork();
        if (pid < 0) {
            printf("Create process error!\n");
            return errno;
        }

        if (pid == 0) { //child
            if (isNewCrawling) {
                printf("start new crawling [%s]\n", si);
                execl(fensbot, "fensbot",
                        "-n", si, "-c", fensbotconfig, "-t", maxTimeLimit, "-scratch",
                        "-o", nodump, NULL);
            } else {
                execl(fensbot, "fensbot",
                        "-n", si, "-c", fensbotconfig, "-t", maxTimeLimit,
                        "-o", nodump, NULL);
            }
            _exit(127);
        } else { // parent
            //printf("main process, create child process(%d)\n", pid);
        }
    }

    _exit(0);
}

static void stopCrawler()
{
    system("ps -efww|grep fensbot|grep -v grep|awk '{print $2}'|xargs kill -9");
    sleep(2);
}

