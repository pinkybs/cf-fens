#include <unistd.h>
#include <signal.h>
#include <time.h>
#include <netdb.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

#include "options.h"

#include "global.h"
#include "utils/text.h"
#include "fetch/checker.h"
#include "fetch/sequencer.h"
#include "fetch/fetchOpen.h"
#include "fetch/fetchPipe.h"
#include "interf/input.h"
#include "interf/output.h"
#include "utils/mypthread.h"

#include "utils/debug.h"
#include "utils/webserver.h"
#include "utils/histogram.h"

static void crawlerStart();
static void crawlerStop();
static void cron();

///////////////////////////////////////////////////////////
#ifdef FENS_OPT_PROF
static bool stop = false;
static void handler (int i) {
    printf("get signal [%d], crawler will go to stop.\n", i);
#ifdef FENS_OPT_RELOAD
        global::seen->save();
#ifdef FENS_OPT_NO_DUP
        global::hDuplicate->save();
#endif
#endif
    stop = true;
}
#endif // FENS_OPT_PROF




// wait to limit bandwidth usage
#ifdef FENS_OPT_MAX_BANDWIDTH
static void waitBandwidth (time_t *old) {
    while (global::remainBand < 0) {
        poll(NULL, 0, 10);
        global::now = time(NULL);
        if (*old != global::now) {
            *old = global::now;
            cron ();
        }
    }
}
#else
#define waitBandwidth(x) ((void) 0)
#endif // FENS_OPT_MAX_BANDWIDTH
#ifndef FENS_OPT_NDEBUG
static uint count = 0;
#endif // FENS_OPT_NDEBUG
///////////////////////////////////////////////////////////
// If this thread terminates, the whole program exits
int main(int argc, char *argv[])
{
    // create all the structures
    global glob(argc, argv);
#ifdef FENS_OPT_PROF
    //SIGKILL(9)SIGSTOP(19) can not catch
    signal (SIGHUP, handler);   // (1)
    signal (SIGINT, handler);   // (2)
    signal (SIGQUIT, handler);  // (3)
    signal (SIGABRT, handler);  // (6)
    signal (SIGTERM, handler);  // (15)
    //signal (SIGCONT, handler);  // (18)
#endif // FENS_OPT_PROF
#ifndef FENS_OPT_NOWEBSERVER
    // launch the webserver if needeed
    if (global::httpPort != 0)
        startThread(startWebserver, NULL);
#endif // FENS_OPT_NOWEBSERVER
    time_t startTime = global::now;
    // Start the search
    crawlerStart();

    time_t old = global::now;

#ifdef FENS_OPT_START_URL_FROM_DATABASE
    global::loadStartUrlsFromDB();
#endif // FENS_OPT_START_URL_FROM_DATABASE
    for (;;) {
        // update time
        global::now = time(NULL);

        if (old != global::now) {
            // this block is called every second

            // if maxTimeLimit has been set (great than 0)
            // check time is limit
            if (global::maxTimeLimit > 0 && (global::now - startTime) > global::maxTimeLimit) {
                crawlerStop();
            }

            old = global::now;
            cron();
        }
        stateMain(-count);
        waitBandwidth(&old);
        stateMain(1);
        for (int i = 0; i < global::maxFds; i++)
            global::ansPoll[i] = 0;
        for (uint i = 0; i < global::posPoll; i++)
            global::ansPoll[global::pollfds[i].fd] = global::pollfds[i].revents;
        global::posPoll = 0;
        stateMain(2);
        input();
        stateMain(3);
        sequencer();
        stateMain(4);
        fetchDns();
        stateMain(5);
        fetchOpen();
        stateMain(6);
        checkAll();
        // select
        stateMain(count++);
        poll(global::pollfds, global::posPoll, 10);
        stateMain(7);
    }
}

static void crawlerStart()
{
    struct tm* timeinfo = localtime(&(global::now));
    printf("[%d-%02d-%02d %02d:%02d:%02d] %s process[%d] is starting its search\n",
            1900 + timeinfo->tm_year, 1 + timeinfo->tm_mon, timeinfo->tm_mday,
            timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec,
            global::userAgent, global::ProcessNum);
}

static void crawlerStop()
{
    struct tm* timeinfo = localtime(&(global::now));
    printf("[%d-%02d-%02d %02d:%02d:%02d] %s process[%d] is stoping its search\n",
            1900 + timeinfo->tm_year, 1 + timeinfo->tm_mon, timeinfo->tm_mday,
            timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec,
            global::userAgent, global::ProcessNum);

    exit(0);
}

// a lot of stats and profiling things
static void cron()
{
    // shall we stop
#ifdef FENS_OPT_PROF
    if (stop) exit(2);
#endif // FENS_OPT_PROF
#ifdef FENS_OPT_START_URL_FROM_DATABASE
    if (global::URLsDisk->getLength() == 0
            && global::URLsDiskWait->getLength() == 0) {
        global::loadStartUrlsFromDB();
    }
#endif

#ifdef FENS_OPT_EXIT_AT_END
    if (global::URLsDisk->getLength() == 0
            && global::URLsDiskWait->getLength() == 0
            && debUrl == 0) {
        //exit(0);
        crawlerStop();
    }
#endif // FENS_OPT_EXIT_AT_END
    // look for timeouts
    checkTimeout();
    // see if we should read again urls in fifowait
    if ((global::now % 30) == 0) {
        global::readPriorityWait = global::URLsPriorityWait->getLength();
        global::readWait = global::URLsDiskWait->getLength();
    }
    if ((global::now % 30) == 15) {
        global::readPriorityWait = 0;
        global::readWait = 0;
    }

#ifdef FENS_OPT_MAX_BANDWIDTH
    // give bandwidth
    if (global::remainBand > 0) {
        global::remainBand = FENS_OPT_MAX_BANDWIDTH;
    } else {
        global::remainBand = global::remainBand + FENS_OPT_MAX_BANDWIDTH;
    }
#endif // FENS_OPT_MAX_BANDWIDTH
#ifndef FENS_OPT_NOSTATS
    histoHit(pages, answers[success]);

    if ((global::now & 7) == 0) {
        urlsRate = (urls - urlsPrev) >> 3;
        urlsPrev = urls;
        pagesRate = (pages - pagesPrev) >> 3;
        pagesPrev = pages;
        successRate = (answers[success] - successPrev) >> 3;
        successPrev = answers[success];
        siteSeenRate = (siteSeen - siteSeenPrev) >> 3;
        siteSeenPrev = siteSeen;
        siteDNSRate = (siteDNS - siteDNSPrev) >> 3;
        siteDNSPrev = siteDNS;
#ifndef FENS_OPT_NDEBUG
        readRate = (byte_read - readPrev) >> 3;
        readPrev = byte_read;
        writeRate = (byte_write - writePrev) >> 3;
        writePrev = byte_write;
#endif // FENS_OPT_NDEBUG
#ifdef FENS_OPT_STATS
        printf("\n%surls : %d  (rate : %d)\npages : %d  (rate : %d)\nsuccess : %d  (rate : %d)\n",
                ctime(&global::now), urls, urlsRate, pages, pagesRate,
                answers[success], successRate);
#endif // FENS_OPT_STATS
    }
#endif // FENS_OPT_NOSTATS
}
