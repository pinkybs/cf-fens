#include <cstdio>
#include <cstring>
#include <cerrno>
#include "utils/EncodingConverter.h"

using namespace std;
using namespace fens;

EncodingConverter::EncodingConverter(const string& from, const string& to)
        throw (Exception)
{
    in_charset = new string(from);
    out_charset = new string(to);
    //iconv_open(const char* out_charset, const char* in_charset)
    mIconvDescriptor = iconv_open(to.c_str(), from.c_str());
    if (mIconvDescriptor == (iconv_t)(-1)) {
        const char* error_str = strerror(errno);
        int size = strlen(error_str) + from.length() + to.length() + 26;
        char error[size];
        snprintf(error, size, "Can't convert from %s to %s: %s", from.c_str(),
                to.c_str(), error_str);
        throw Exception(error);
    }
}

EncodingConverter::~EncodingConverter()
{
    delete in_charset;
    delete out_charset;
    iconv_close(mIconvDescriptor);
}

string EncodingConverter::convert(const string& input)
{
    const char* inbuf = input.c_str();
    size_t in_len = input.length();

    char* result;
    size_t out_len;
    EncodingConverterError retval = _iconv(inbuf, in_len, &result, &out_len);
    if (retval == ENCONDING_CONVERTER_ERROR_UNKNOWN) {
        return string();
    }
    string out(result, out_len);
    free(result);

    return out;
}

EncodingConverterError EncodingConverter::_iconv(const char* in_p,
        size_t in_len, char** out, size_t* out_len)
{
    char *pinbuf = const_cast<char*> (in_p);
    size_t in_left, out_size, out_left;
    char *out_p, *out_buf, *tmp_buf;
    size_t bsz, result = 0;
    EncodingConverterError retval = ENCONDING_CONVERTER_ERROR_SUCCESS;

    in_left = in_len;
    out_left = in_len + 32; /* Avoid realloc() most cases */
    out_size = 0;
    bsz = out_left;
    out_buf = (char *) malloc(bsz + 1);
    out_p = out_buf;

    while (in_left > 0) {
        result = iconv(mIconvDescriptor, (char **) &pinbuf, &in_left,
                (char **) &out_p, &out_left);
        out_size = bsz - out_left;
        if (result == (size_t)(-1)) {
            if (in_left > 0) {
                if (errno == E2BIG) {
                    /* converted string is longer than out buffer */
                    bsz += in_len;
                    tmp_buf = (char*) realloc(out_buf, bsz + 1);
                    if (tmp_buf != NULL) {
                        out_p = out_buf = tmp_buf;
                        out_p += out_size;
                        out_left = bsz - out_size;
                        continue;
                    }
                }
                //fixed, if one character byte can not been converted
                //forrward and continue do
                else if (errno == EILSEQ) {
                    // forward one illegal byte
                    in_left = in_len - (pinbuf - in_p);
                    in_left--;
                    pinbuf++;
                    continue;
                }
            }
        }
        break;
    }
    if (result != (size_t)(-1)) {
        /* flush the shift-out sequences */
        for (;;) {
            result = iconv(mIconvDescriptor, NULL, NULL, (char **) &out_p,
                    &out_left);
            out_size = bsz - out_left;
            if (result != (size_t)(-1)) {
                break;
            }
            if (errno == E2BIG) {
                bsz += 16;
                tmp_buf = (char *) realloc(out_buf, bsz);
                if (tmp_buf == NULL) {
                    break;
                }
                out_p = out_buf = tmp_buf;
                out_p += out_size;
                out_left = bsz - out_size;
            } else {
                break;
            }
        }
    }

    if (result == (size_t)(-1)) {
        switch (errno) {
            case EINVAL:
                retval = ENCONDING_CONVERTER_ERROR_ILLEGAL_CHAR;
                break;
            case EILSEQ:
                retval = ENCONDING_CONVERTER_ERROR_ILLEGAL_SEQ;
                break;
            case E2BIG:
                retval = ENCONDING_CONVERTER_ERROR_TOO_BIG;
                break;
            default:
                /* other error */
                retval = ENCONDING_CONVERTER_ERROR_UNKNOWN;
                free(out_buf);
                return ENCONDING_CONVERTER_ERROR_UNKNOWN;
        }
    }
    *out_p = '\0';
    *out = out_buf;
    *out_len = out_size;

    return retval;
}
