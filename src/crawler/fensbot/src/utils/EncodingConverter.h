#ifndef __FENS_ENCODING_CONVERTER_H__
#define __FENS_ENCODING_CONVERTER_H__

#include <iconv.h>
#include <string>
#include <stdexcept>

namespace fens
{

    enum EncodingConverterError
    {
        ENCONDING_CONVERTER_ERROR_SUCCESS = 0,
        ENCONDING_CONVERTER_ERROR_CONVERTER = 1,
        ENCONDING_CONVERTER_ERROR_WRONG_CHARSET = 2,
        ENCONDING_CONVERTER_ERROR_TOO_BIG = 3,
        ENCONDING_CONVERTER_ERROR_ILLEGAL_SEQ = 4,
        ENCONDING_CONVERTER_ERROR_ILLEGAL_CHAR = 5,
        ENCONDING_CONVERTER_ERROR_UNKNOWN = 6,
        ENCONDING_CONVERTER_ERROR_MALFORMED = 7,
        ENCONDING_CONVERTER_ERROR_ALLOC = 8
    };

    class EncodingConverter
    {
    public:
        class Exception: public std::runtime_error
        {
        public:
            Exception(const std::string& arg) :
                std::runtime_error(arg)
            {
            }
        };

        EncodingConverter(const std::string& from, const std::string& to)
                throw (Exception);
        ~EncodingConverter();

        std::string convert(const std::string& input);

        inline std::string& getInCharset() const
        {
            return *in_charset;
        }
        inline std::string& getOutCharset() const
        {
            return *out_charset;
        }

    private:
        EncodingConverterError _iconv(const char* in_p, size_t in_len,
                char** out, size_t* out_len);

        iconv_t mIconvDescriptor;
        std::string* in_charset;
        std::string* out_charset;
    };
}

#endif // __FENS_ENCODING_CONVERTER_H__
