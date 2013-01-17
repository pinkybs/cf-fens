/* histogram of number of pages retrieved for graphical stats */

#ifndef __FENS_HISTOGRAM_H__
#define __FENS_HISTOGRAM_H__

#include "options.h"

#ifdef FENS_OPT_GRAPH

/* call this every sec to report downloads */
void histoHit (uint p, uint s);

/* call this in webserver for printing */
void histoWrite (int fds);

#else // FENS_OPT_GRAPH NOT DEFINED
#define histoHit(p,s) ((void) 0)
#define histoWrite(f) ((void) 0)

#endif // FENS_OPT_GRAPH
#endif // __FENS_HISTOGRAM_H__
