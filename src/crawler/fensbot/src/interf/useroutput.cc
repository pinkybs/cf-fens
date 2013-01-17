/* This is the file you should change if you want to
 * use the data fetched by fensbot.
 *
 * See useroutput.h for the interface
 *
 * See the files XXXuserouput.cc for examples */

#include "options.h"

#ifdef FENS_OPT_SIMPLE_SAVE
#include "interf/saveuseroutput.cc"

#elif defined(FENS_OPT_MIRROR_SAVE)
#include "interf/mirrorsaveuseroutput.cc"

#elif defined(FENS_OPT_STATS_OUTPUT)
#include "interf/statsuseroutput.cc"

#elif defined(FENS_OPT_DATABASE_SAVE)
#include "interf/dbsaveuseroutput.cc"

#else // FENS_OPT_DEFAULT_OUTPUT
#include "interf/defaultuseroutput.cc"

#endif
