1. htmlcxx
./confingure --prefix=/usr/local/htmlcxx
make & make install
ln -s /usr/local/htmlcxx/lib/libhtmlcxx.so.3 /usr/lib/libhtmlcxx.so.3

2. apr
tar zxf apr-1.3.7.tar.gz
cd apr-1.3.7
./confingure --prefix=/usr/local/apr
make & make install

3. apr-util
tar zxf apr-util-1.3.8.tar.gz
cd apr-util-1.3.8
./confingure --prefix=/usr/local/apr-util --with-apr=/usr/local/apr
make & make install

4. log4cxx
tar zxf apache-log4cxx-0.10.0.tar.gz
cd apache-log4cxx-0.10.0
./confingure --prefix=/usr/local/log4cxx --with-apr=/usr/local/apr --with-apr-util=/usr/local/apr-util --with-charset=utf-8
make & make install
ln -s /usr/local/log4cxx/lib/liblog4cxx.so.10 /usr/lib/liblog4cxx.so.10

5.
ln -s /usr/local/mysql/lib/mysql/libmysqlclient.so.15 /usr/lib/libmysqlclient.so.15