#ifndef __FENS_FETCHOPEN_H__
#define __FENS_FETCHOPEN_H__

/* Opens sockets
 * this function perform dns calls, using adns
 */
void fetchDns();

/* Opens sockets
 * Never block (only opens sockets on allready known sites)
 * work inside the main thread
 */
void fetchOpen();

#endif // __FENS_FETCHOPEN_H__
