#ifndef __FENS_CHECKER_H__
#define __FENS_CHECKER_H__

#include "types.h"
#include "utils/Vector.h"

/** check if an url is allready known
 * if not send it
 * @param u the url to check
 */
void check(url *u);

/** Check the extension of an url
 * @return true if it might be interesting, false otherwise
 */
bool filter1(char *host, char *file);

#endif // __FENS_CHECKER_H__
