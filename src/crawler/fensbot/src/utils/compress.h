#ifndef __FENS_COMPRESS_H__
#define __FENS_COMPRESS_H__

/* Win32 needs some more memory */
#ifdef WIN32
#define COMPRESS_ZLIB_MODIFIER 100
#else
#define COMPRESS_ZLIB_MODIFIER 1000
#endif // WIN32
#include <string>
#include <zlib.h>

namespace fens
{

    /* Gzip-compress a string */
    std::string gzcompress(const std::string &source, int level =
            Z_DEFAULT_COMPRESSION);
    /* Unzip a gzip-compressed string */
    std::string gzuncompress(const std::string &source, unsigned long length =
            0);

    /*Gzip-compress a string */
    std::string gzdeflate(const std::string &source, int level =
            Z_DEFAULT_COMPRESSION);
    /* Unzip a gzip-compressed string */
    std::string gzinflate(const std::string &source, unsigned long length = 0);

}

#endif // __FENS_COMPRESS_H__
