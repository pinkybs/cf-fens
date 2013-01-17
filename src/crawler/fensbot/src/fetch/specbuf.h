/* This is a really bad hack, but i have not found a better way
 * for doing this...
 *
 * This file is included in the definition of class html
 * You can put inside definitions you need for specificBuf
 */

#ifdef FENS_OPT_SAVE_SPECIFIC
#include "fetch/savespecbuf.h"

#elif defined(FENS_OPT_DYNAMIC_SPECIFIC)
#include "fetch/dynamicspecbuf.h"

#else // FENS_OPT_DEFAULT_SPECIFIC
// nothing special

#endif
