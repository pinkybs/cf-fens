/* class hashTable
 * This class is in charge of making sure we don't crawl twice the same url
 */

#ifndef __FENS_HASHTABLE_H__
#define __FENS_HASHTABLE_H__

#include "types.h"
#include "utils/url.h"

class hashTable
{
private:
    ssize_t size;
    char *table;

public:
    /* constructor */
    hashTable(bool create);

    /* destructor */
    ~hashTable();

    /* save the hashTable in a file */
    void save();

    /* test if this url is allready in the hashtable
     * return true if it has been added
     * return false if it has allready been seen
     */
    bool test(url *U);

    /* set a url as present in the hashtable
     */
    void set(url *U);

    /* add a new url in the hashtable
     * return true if it has been added
     * return false if it has allready been seen
     */
    bool testSet(url *U);
};

#endif // __FENS_HASHTABLE_H__
