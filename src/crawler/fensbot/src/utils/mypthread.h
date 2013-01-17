#ifndef __FENS_MYTHREAD_H__
#define __FENS_MYTHREAD_H__

#include <pthread.h>

#include "options.h"

#ifdef FENS_OPT_THREAD_OUTPUT

#define mypthread_cond_init(x,y) pthread_cond_init(x,y)
#define mypthread_cond_destroy(x) pthread_cond_destroy(x)
#define mypthread_cond_wait(c,x,y) while (c) { pthread_cond_wait(x,y); }
#define mypthread_cond_broadcast(x) pthread_cond_broadcast(x)

#define mypthread_mutex_init(x,y) pthread_mutex_init(x,y)
#define mypthread_mutex_destroy(x) pthread_mutex_destroy(x)
#define mypthread_mutex_lock(x) pthread_mutex_lock(x)
#define mypthread_mutex_unlock(x) pthread_mutex_unlock(x)

#else

#define mypthread_cond_init(x,y) ((void) 0)
#define mypthread_cond_destroy(x) ((void) 0)
#define mypthread_cond_wait(c,x,y) ((void) 0)
#define mypthread_cond_broadcast(x) ((void) 0)

#define mypthread_mutex_init(x,y) ((void) 0)
#define mypthread_mutex_destroy(x) ((void) 0)
#define mypthread_mutex_lock(x) ((void) 0)
#define mypthread_mutex_unlock(x) ((void) 0)

#endif // FENS_OPT_THREAD_OUTPUT
typedef void* (*StartFun)(void *);
void startThread(StartFun run, void *arg);

#endif // __FENS_MYTHREAD_H__
