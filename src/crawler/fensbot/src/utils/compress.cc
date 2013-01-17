#include "utils/compress.h"

using namespace fens;

/* Gzip-compress a string */
std::string gzcompress(const std::string &source, int level =
        Z_DEFAULT_COMPRESSION)
{
    const Bytef* data = reinterpret_cast<const Bytef*> (source.c_str());
    size_t data_len = source.length();
    int status;
    unsigned long l2;
    Bytef* s2;

    if ((level < -1) || (level > 9)) {
        return std::string();
    }

    l2 = data_len + (data_len / COMPRESS_ZLIB_MODIFIER) + 15 + 1; /* room for \0 */
    s2 = (Bytef*) malloc(l2);
    if (!s2) {
        return std::string();
    }

    if (level >= 0) {
        status = compress2(s2, &l2, data, data_len, level);
    } else {
        status = compress(s2, &l2, data, data_len);
    }

    if (status == Z_OK) {
        //s2 = realloc(s2, l2 + 1); /* space for \0 */
        //s2[l2] = '\0';
        std::string out((char*) s2, l2);
        free(s2);
        return out;
    } else {
        free(s2);
        return std::string();
    }
}

/* Unzip a gzip-compressed string */
std::string gzuncompress(const std::string &source, unsigned long limit = 0)
{
    const Bytef* data = reinterpret_cast<const Bytef*> (source.c_str());
    size_t data_len = source.length();
    int status;
    unsigned int factor = 1, maxfactor = 16;
    unsigned long length;
    Bytef* s1 = NULL;
    Bytef* s2 = NULL;

    /*
     zlib::uncompress() wants to know the output data length
     if none was given as a parameter
     we try from input length * 2 up to input length * 2^15
     doubling it whenever it wasn't big enough
     that should be eneugh for all real life cases
     */
    do {
        length = limit ? limit : (unsigned long) data_len * (1 << factor++);
        s2 = (Bytef*) realloc(s1, length);
        status = uncompress(s2, &length, data, data_len);
        s1 = s2;
    } while ((status == Z_BUF_ERROR) && (!limit) && (factor < maxfactor));

    if (status == Z_OK) {
        //s2 = erealloc(s2, length + 1); /* space for \0 */
        //s2[length] = '\0';
        std::string out((char*) s2, length);
        free(s2);
        return out;
    } else {
        free(s2);
        return std::string();
    }
}

/*Gzip-compress a string */
std::string gzdeflate(const std::string &source, int level =
        Z_DEFAULT_COMPRESSION)
{
    const Bytef* data = reinterpret_cast<const Bytef*> (source.c_str());
    size_t data_len = source.length();
    int status;
    z_stream stream;
    Bytef* s2;

    if ((level < -1) || (level > 9)) {
        return std::string();
    }

    stream.data_type = Z_ASCII;
    stream.zalloc = (alloc_func) Z_NULL;
    stream.zfree = (free_func) Z_NULL;
    stream.opaque = (voidpf) Z_NULL;

    stream.next_in = (Bytef *) data;
    stream.avail_in = data_len;

    stream.avail_out = stream.avail_in + (stream.avail_in
            / COMPRESS_ZLIB_MODIFIER) + 15 + 1; /* room for \0 */

    s2 = (Bytef*) malloc(stream.avail_out);
    if (!s2) {
        return std::string();
    }

    stream.next_out = s2;

    /* init with -MAX_WBITS disables the zlib internal headers */
    status = deflateInit2(&stream, level, Z_DEFLATED, -MAX_WBITS,
            MAX_MEM_LEVEL, 0);
    if (status == Z_OK) {
        status = deflate(&stream, Z_FINISH);
        if (status != Z_STREAM_END) {
            deflateEnd(&stream);
            if (status == Z_OK) {
                status = Z_BUF_ERROR;
            }
        } else {
            status = deflateEnd(&stream);
        }
    }

    if (status == Z_OK) {
        //s2 = realloc(s2,stream.total_out + 1); /* resize to buffer to the "right" size */
        //s2[stream.total_out] = '\0';
        std::string out((char*) s2, stream.total_out);
        free(s2);
        return out;
    } else {
        free(s2);
        return std::string();
    }
}

/* Unzip a gzip-compressed string */
std::string gzinflate(const std::string &source, unsigned long limit = 0)
{
    const Bytef* data = reinterpret_cast<const Bytef*> (source.c_str());
    size_t data_len = source.length();
    int status;
    unsigned int factor = 1, maxfactor = 16;
    unsigned long length;
    Bytef* s1 = NULL;
    Bytef* s2 = NULL;
    z_stream stream;

    /*
     stream.avail_out wants to know the output data length
     if none was given as a parameter
     we try from input length * 2 up to input length * 2^15
     doubling it whenever it wasn't big enough
     that should be enaugh for all real life cases
     */

    stream.zalloc = (alloc_func) Z_NULL;
    stream.zfree = (free_func) Z_NULL;

    do {
        length = limit ? limit : (unsigned long) data_len * (1 << factor++);
        s2 = (Bytef*) realloc(s1, length);

        if (!s2 && s1) {
            free(s1);
            return std::string();
        }

        stream.next_in = (Bytef *) data;
        stream.avail_in = (uInt) data_len + 1; /* there is room for \0 */

        stream.next_out = s2;
        stream.avail_out = (uInt) length;

        /* init with -MAX_WBITS disables the zlib internal headers */
        status = inflateInit2(&stream, -MAX_WBITS);
        if (status == Z_OK) {
            status = inflate(&stream, Z_FINISH);
            if (status != Z_STREAM_END) {
                inflateEnd(&stream);
                if (status == Z_OK) {
                    status = Z_BUF_ERROR;
                }
            } else {
                status = inflateEnd(&stream);
            }
        }
        s1 = s2;

    } while ((status == Z_BUF_ERROR) && (!limit) && (factor < maxfactor));

    if (status == Z_OK) {
        //s2 = realloc(s2, stream.total_out + 1); /* room for \0 */
        //s2[stream.total_out] = '\0';
        std::string out((char*) s2, stream.total_out);
        free(s2);
        return out;
    } else {
        free(s2);
        return std::string();
    }
}
