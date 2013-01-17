// this file is included in the definition of class html (file.h)

/** file descriptor number */
int fdsSpec;
/** number of bits written */
int nbSpec;
/** open file descriptor */
void newSpec();
/** feed file descriptor,
 * return true and set errno = tooBig if necessary
 */
bool pipeSpec();
/** get the content of the page */
char *getContent();
/** number of file and directory when saved on disk */
int mydir;
int myfile;
