#ifndef __FENS_STRING_H__
#define __FENS_STRING_H__

#include "types.h"
#include "utils/debug.h"

namespace fens
{

    class FensString
    {
    private:
        char *chaine;
        uint pos;
        uint size;
    public:
        // Constructor
        FensString(uint size = FENS_STRING_SIZE);
        // Destructor
        ~FensString();
        // Recycle this string
        void recycle(uint size = FENS_STRING_SIZE);
        // get the char * : it is deleted when you delete this String Object
        char *getString();
        // give a char * : it creates a new one : YOU MUST DELETE IT YOURSELF
        char *giveString();
        // append a char
        void addChar(char c);
        // append a char *
        void addString(char *s);
        // append a buffer
        void addBuffer(char *s, uint len);
        // length of this string
        inline uint getLength()
        {
            return pos;
        }
        ;
        // get a char of this string
        inline char operator [](uint i)
        {
            return chaine[i];
        }
        // change a char
        void setChar(uint i, char c);
    };

}

#endif // __FENS_STRING_H__
