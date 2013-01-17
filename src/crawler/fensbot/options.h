#ifndef __FENS_CONFIG_H__
#define __FENS_CONFIG_H__

#include "config.h"

/////////////////////////////////////////////////////////////
// Select the output module you want to use

//#define FENS_OPT_DEFAULT_OUTPUT   // do nothing...
//#define FENS_OPT_SIMPLE_SAVE      // save in files named save/dxxxxxx/fyyyyyy
//#define FENS_OPT_MIRROR_SAVE      // save in files (respect sites hierarchy)
//#define FENS_OPT_STATS_OUTPUT     // do some stats on pages
#define FENS_OPT_DATABASE_SAVE      // save in database

////////////////////////////////////////////////////////////
// Set up a specific search

//#define FENS_OPT_SPECIFICSEARCH
//#define contentTypes ((char *[]) { "audio/mpeg", NULL })
//#define privilegedExts ((char *[]) { ".mp3", NULL })

// how do you want to manage specific pages (select one of the followings)
//#define FENS_OPT_DEFAULT_SPECIFIC
//#define FENS_OPT_SAVE_SPECIFIC
//#define FENS_OPT_DYNAMIC_SPECIFIC


//////////////////////////////////////////////////////////
// What do you want the crawler to do

// do you want to follow links in pages
#define FENS_OPT_FOLLOW_LINKS

// do you want the crawler to associate to each page the list of its sons
//#define FENS_OPT_LINKS_INFO

// do you want to associate a tag to pages (given in input)
// this allows to follow a page from input to output (and follow redirection)
//#define FENS_OPT_URL_TAGS

// do you want to suppress duplicate pages
//#define FENS_OPT_NO_DUP

// do you want fensbot to stop when everything has been fetched
#define FENS_OPT_EXIT_AT_END

// do you want to fetch images
// if you enable this option, update forbiddenExtensions in fensbot.conf
//#define FENS_OPT_IMAGES

// downlaod everything (ie no check of content type in http headers)
//#define FENS_OPT_ANYTYPE

// do you want to manage cookies
//#define FENS_OPT_COOKIES

// added for fens url, siteid
#define FENS_OPT_URL_SITEID
// added for fens, get startUrl form database
#define FENS_OPT_START_URL_FROM_DATABASE


//////////////////////////////////////////////////////////
// Various options

// do you want to get cgi (dynamic param: ?key=value&p)
// 0 : yes ; 1 : no ; 2 : NO !
#define FENS_OPT_CGILEVEL 0

// limit bandwith usage (in octets/sec)
// be carefull, fensbot might use 10 to 20% more
//#define FENS_OPT_MAX_BANDWIDTH 200000

// the depth is initialized each time a link goes to another site
#define FENS_OPT_DEPTHBYSITE


//////////////////////////////////////////////////////////
// Efficiency vs feature

// do we need a special thread for output
// This is compulsory if it can block
// (not needed if you did not add code yourself)
#define FENS_OPT_THREAD_OUTPUT

// if this option is set, fensbot saves the hashtable from time to time
// this way it can restart from where it last stopped
// by reloading the table
#define FENS_OPT_RELOAD

// signal INT = 2
#define FENS_OPT_PROF

//////////////////////////////////////////////////////////
// now it's just if you need to know how it works

// do not launch the webserver
// this can be usefull in order to launch no thread at all
#define FENS_OPT_NOWEBSERVER

// do you want nice graphs for in the stats page
#define FENS_OPT_GRAPH

// uncomment if you are not interested in debugging information
//#define FENS_OPT_NDEBUG

// enable this if you really dislike stats (in the webserver)
//#define FENS_OPT_NOSTATS

// enable this if you really like stats (on stdout)
//#define FENS_OPT_STATS
//#define FENS_OPT_BIGSTATS

// Please enable this option if you want to report a crash
// then compile with "make debug"
//#define FENS_OPT_CRASH

#endif // __FENS_CONFIG_H__
