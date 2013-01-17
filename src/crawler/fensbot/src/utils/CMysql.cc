#include <cstdio>

#include "utils/CMysql.h"

using namespace fens;

void CMysql::init(const char* host, const char* user, const char* passwd,
        const char* db, unsigned int port, const char* charset)
{
    _host = host;
    _user = user;
    _passwd = passwd;
    _db = db;
    _port = port;
    _charset = charset;
    _connected = false;

    connect();
}

CMysql::CMysql(const char* host, const char* user, const char* passwd,
        const char* db)
{
    init(host, user, passwd, db, 3306, "utf8");
}

CMysql::CMysql(const char* host, const char* user, const char* passwd,
        const char* db, unsigned int port, const char* charset)
{
    init(host, user, passwd, db, port, charset);
}

CMysql::~CMysql()
{
    close();
}

bool CMysql::isConnected()
{
    return _connected;
}

bool CMysql::connect()
{
    _mysql = mysql_init(NULL);
    if (!_mysql) {
        printf("\n Failed to initate Mysql connection. ");
        return false;
    }

    if (!mysql_real_connect(_mysql, _host, _user, _passwd, _db, _port, NULL, 0)) {
        printf("Failed to connect to MySQL: Error: %s\n ", mysql_error(_mysql));
        return false;
    }

    // set character
    mysql_set_character_set(_mysql, _charset);

    _connected = true;
    return _connected;
}

void CMysql::close()
{
    if (_connected) {
        mysql_close(_mysql);
        _connected = false;
    }
}

size_t CMysql::escapeString(std::string* ps, const char* original,
        size_t length)
{
    if (ps == 0) {
        // Can't do any real work!
        return 0;
    } else if (original == 0) {
        // ps must point to the original data as well as to the
        // receiving string, so get the pointer and the length from it.
        original = ps->data();
        length = ps->length();
    } else if (length == 0) {
        // We got a pointer to a C++ string just for holding the result
        // and also a C string pointing to the original, so find the
        // length of the original.
        length = strlen(original);
    }

    char* escaped = new char[length * 2 + 1];
    length = mysql_real_escape_string(_mysql, escaped, original, length);
    ps->assign(escaped, length);
    delete[] escaped;

    return length;
}

std::string CMysql::escapeString(const std::string& source)
{
    size_t length = source.length();
    if (length == 0) {
        return std::string();
    }

    char* escaped = new char[length * 2 + 1];
    length = mysql_real_escape_string(_mysql, escaped, source.c_str(), length);
    std::string out(escaped, length);
    delete[] escaped;

    return out;
}

bool CMysql::query(const char* sql)
{
    if (!isConnected()) {
        printf("\n connection is disconected!!!");
        return false;
    }

    //return mysql_real_query(_mysql, sql, strlen(sql)) == 0;
    int b =  mysql_real_query(_mysql, sql, strlen(sql)) == 0;
    if (!b) {
        printf("Error making query: %s\n", mysql_error(_mysql));
    }

    return b;
}

MYSQL_RES* CMysql::fetch(const char* sql)
{
    MYSQL_RES* rs = NULL;
    if (query(sql)) {
        rs = mysql_store_result(_mysql);
    } else {
        printf("query failure!\n");
    }

    return rs;
}

void CMysql::free(MYSQL_RES* result)
{
    if (result) {
        mysql_free_result(result);
    }
}
