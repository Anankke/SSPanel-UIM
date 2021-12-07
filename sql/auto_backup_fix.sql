//user替换为填写在 config 中的数据库用户名，localhost 替换为 config 中的 db_host 的值（无需端口）
//在 phpmyadmin 登陆root执行sql或在命令行中登陆root执行sql
//若mysql版本小于5.7.31 & 8.0.21 则不需要执行此sql
GRANT PROCESS ON *.* TO user@localhost;
