import MySQLdb as sql
import TestClass, RunCode
from TestClass import *
from RunCode import *
from classMySQL import *
from variables import *
import logging
import platform, re, os, shutil, sys, thread, time, urllib, SocketServer, subprocess, resource, math
logging.basicConfig(filename='error.log',level=logging.DEBUG)

'''
Run all the test cases for every submission
@param submission_id Submission ID
'''
def RunSub( submission_id ):
	try:
		sql_connection = MySQL()
		
		run_inf = sql_connection.getOne("submission","WHERE id = '" + str(submission_id) + "'")
		problem_id = run_inf['problem_id']
		code_extension = run_inf['language'] 
		path_to_problem = path_problems + "/%d/" % (problem_id)


		test = Test()
		test.parse_file(path_to_problem + "info.txt")
		result = CompileCode(submission_id, code_extension)
		total_ac = 0
		outp_text = ""
		final_result = ""

		if result != CE_SIGNAL:
			for testset in test.test_sets:
				fail = False
				for case_id in testset:
					time_limit = test.test_cases[case_id].time_limit
					memory_limit = test.test_cases[case_id].memory_limit
					input_file = test.test_cases[case_id].input_file
					output_file = test.test_cases[case_id].output_file
					judge_file = test.test_cases[case_id].judge_file
					answer = RunTestCase(time_limit, memory_limit, path_to_problem + input_file, path_to_problem +output_file, judge_file, submission_id)
					if answer != "OK":
						outp_text += answer + ";"
						final_result = answer
						fail = True
						break

				if fail == False:
					total_ac += 1
					outp_text += "OK;"

		else:
			outp_text += "CE;"
			final_result = "CE"

		total_points = float(total_ac) / float(len(test.test_sets))
		total_points *= 100.0
		total_points = int(total_points)

		if total_points == 100:
			final_result = "AC"

		
		sql_connection.update("UPDATE submission SET points = " + str(total_points) + " , status = '"  + outp_text + "' WHERE id = " + str(submission_id))
		try: cursor.close()
		except: pass
		try: link.close()
		except: pass

	except sql.Error, e:
            logging.warning("MySQL Error %d : %s\n" % (e.args[0],e.args[1]))
	
	

class MyTCPHandler( SocketServer.StreamRequestHandler ):

	def handle(self):
		self.data = self.rfile.readline().strip()
		
		data = self.data.split(';')
		
		type_submission = data[0]
		
		if len(data) > 1:
			id_now = data[1]
		else:
			id_now = 0
		
		sql_connection = MySQL()
		
		if type_submission == "JUDGE":
			RunSub(int(id_now))
		elif type_submission == "REJUDGE_SUBMISSION":
			RunSub(int(id_now))
		elif type_submission == "REJUDGE_ALL_USER_SUBMISSIONS":
			
			rows = sql_connection.getAll("submission", "WHERE user_id=" + str(id_now))
			for row in rows:
				RunSub(int(row['id']))
	
		elif type_submission == "REJUDGE_ALL_PROBLEM_SUBMISSIONS":
						
			rows = sql_connection.getAll("submission", "WHERE problem_id=" + str(id_now))
			for row in rows:
				RunSub(int(row['id']))
				
		elif type_submission == "REJUDGE_ALL":
			
			rows = sql_connection.getAll("submission","ORDER BY id ASC")
			for row in rows:
				RunSub(int(row['id']))
				
		sql_connection.closeSql()

if __name__ == "__main__":

	HOST, PORT = "localhost", 8722
	server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)
	server.request_queue_size = 200
	
	try:
		server.serve_forever()
	except KeyboardInterrupt, e:
		logging.warning("Keyboard Interrupt Detected.\n")
	except Exception, e:
		logging.warning("Exception : " + str(e) + "\n")
