from variables import *
import MySQLdb as sql

class MySQL:
	
	def __init__(self):
		self.link = sql.connect(host = sql_hostname,port = sql_hostport,user=sql_username,passwd=sql_password,db=sql_database,unix_socket=u_socket)
			
	def query(self,query_str):
		cursor = self.cursor(sql.cursors.DictCursor)
		cursor.execute(query_str)
		
		rows = cursor.fetchall()
		
		cursor.close()
		
		return rows
		
	def closeSql(self):
		self.link.close()
		
