/* class hashDup
 * This class is in charge of making sure we don't save twice the same content (duplicate)
 */

#ifndef __FENS_HASHDUP_H__
#define __FENS_HASHDUP_H__

class hashDup
{
private:
    ssize_t size;
    char *table;
    char *file;

public:
    /* constructor */
    hashDup(ssize_t size, char *init, bool scratch);

    /* destructor */
    ~hashDup();

    /* set a page in the hashtable
     * return false if it was already there
     * return true if it was not (ie it is new)
     */
    bool testSet(char *doc);

    /* save in a file */
    void save();
};

#endif // __FENS_HASHDUP_H__
