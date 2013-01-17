/* this module defines how specific pages are handled
 * the following functions must be defined :
 * - initSpecific : this function is called when fensbot is launched. This
 *     must be a function because it is defined in file.h.
 * - constrSpec : this function is called in the constructor of class html
 * - newSpec : a new specific page has been discovered
 * - pipeSpec : some data has arrived on the stream
 * - endInput : the end of the input : be careful, this function
 *     might not be called in case of timeout or other reasons
 * - getPage : someone needs the content of the page
 * - getSize : gives the size of the document
 * - destructSpec : this function is called in the destructor of class html
 *
 * All this function are only called if FENS_OPT_SPECIFICSEARCH is defined
 * constrSpec is called for every page
 * other functions are only called if state==SPECIFIC
 */

#ifdef FENS_OPT_SAVE_SPECIFIC
#include "fetch/savespecbuf.cc"

#elif defined(FENS_OPT_DYNAMIC_SPECIFIC)
#include "fetch/dynamicspecbuf.cc"

#else // FENS_OPT_DEFAULT_SPECIFIC
#include "fetch/defaultspecbuf.cc"

#endif
