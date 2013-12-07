## DEFINE VARIABLES ##
sql_hostname = "localhost"
sql_hostport = 3306
sql_username = "mdantas"
sql_password = "multiuso12"
sql_database = "opi_db"
u_socket = "/opt/lampp/var/mysql/mysql.sock"
path_codes = "/var/codes-opijudge"
path_problems = "/var/problems-opijudge"
path_runs = "/var/runs-opijudge"
ioeredirect = "2>error-sub.log"

## EXECUTE JUDGE SETTINGS
TLE_SIGNAL = -1
MLE_SIGNAL = 1
RTE_SIGNAL = -2
AC_SIGNAL = 0
WA_SIGNAL = 3
CE_SIGNAL = 4
##


## COMPILE SECTION
COMPILE_SUCCESS = 5
path_exceptions = ["/lib/i386-linux-gnu/libm.so.6","/lib/i386-linux-gnu/libgcc_s.so.1","/home/mdantas/Desktop", \
		"/etc/ld.so.cache","/lib/i386-linux-gnu/libc.so.6","/usr/lib/i386-linux-gnu/libstdc++.so.6",path_problems]
######################

