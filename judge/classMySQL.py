from variables import *
import MySQLdb as sql

class MySQL:
	
	def __init__(self):
		self.link = sql.connect(host = sql_hostname,port = sql_hostport,user=sql_username,passwd=sql_password,db=sql_database,unix_socket=u_socket)
			
	def getAll(self,table,where_clause):
		cursor = self.link.cursor(sql.cursors.DictCursor)
		cursor.execute("SELECT * FROM '" + str(table) + "' " + where_clause)
		
		rows = cursor.fetchall()
		
		cursor.close()
		
		return rows
		
	def getOne(self,table,where_clause):
		cursor = self.link.cursor(sql.cursors.DictCursor)
		cursor.execute("SELECT * FROM '" + str(table) + "' " + where_clause)
		
		rows = cursor.fetchone()
		
		cursor.close()
		
		return rows
		
	def update(self, query_str):
		cursor = self.link.cursor(sql.cursors.DictCursor)
		cursor.execute(query_str)
		cursor.close()
		
	def closeSql(self):
		self.link.close()
		
