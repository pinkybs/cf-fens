#ifndef __FENS_CMYSQL_H__
#define __FENS_CMYSQL_H__

#include <string>
#include <mysql.h>

namespace fens
{

    class CMysql
    {
    public:
        CMysql(const char* host, const char* user, const char* passwd,
                const char* db);
        CMysql(const char* host, const char* user, const char* passwd,
                const char* db, unsigned int port, const char* charset);
        ~CMysql();
        size_t escapeString(std::string* ps, const char* original,
                size_t length);
        std::string escapeString(const std::string& source);
        bool query(const char* sql);
        size_t lastInsertId();
        MYSQL_RES* fetch(const char* sql);
        void free(MYSQL_RES* result);

    private:
        MYSQL* _mysql;
        bool _connected;
        const char* _host;
        const char* _user;
        const char* _passwd;
        const char* _db;
        unsigned int _port;
        const char* _charset;
        void init(const char* host, const char* user, const char* passwd,
                const char* db, unsigned int port, const char* charset);

    protected:
        bool connect();
        void close();
        bool isConnected();
    };

}

#endif //__FENS_CMYSQL_H__
