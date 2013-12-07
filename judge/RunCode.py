import platform, re, os, shutil, sys, thread, time, urllib, SocketServer, subprocess, resource, math
import signal
from sandbox import *
from sandboxpolicy import SelectiveOpenPolicy
from variables import *
import logging


logging.basicConfig(filename='error.log',level=logging.DEBUG)

'''
Read some file
@param filename File Name
'''
def file_read(filename):
	if not os.path.exists(filename): return "";
	f = open(filename,"r"); d = f.read(); f.close(); return d.replace("\r","")
	
'''
Write to some file
@param filename File Name
@param data Content to write
'''
def file_write(filename,data):
	f = open(filename,"w"); f.write(data.replace("\r","")); f.close();

'''
Compile the code
@param submission_id Submission ID
@param language Which language is being used
@return CE, COMPILE_SUCCESS
'''
def CompileCode( submission_id, language ):
	
	if( language not in ('c', 'cpp', 'pas') ): return CE_SIGNAL

	result = COMPILE_SUCCESS

	if os.path.exists(path_codes + str(submission_id)):
		os.system("rm -r " + path_codes + str(submission_id))
	if language == "c":
		os.system("gcc " + path_codes + str(submission_id) + ".c -lm -lcrypt -O2 -pipe -ansi -ONLINE_JUDGE -w -o " + path_codes + str(submission_id) + " " + ioeredirect)
	elif language == "cpp":
		os.system("g++ " + path_codes + str(submission_id) + ".cpp -o " + path_codes + str(submission_id) + " " + ioeredirect)
	elif language == "pas":
		os.system("fpc " + path_codes + str(submission_id) + ".pas -o " + path_codes + str(submission_id) + " " + ioeredirect)
	
	if not os.path.exists(path_codes + str(submission_id)): result = CE_SIGNAL
	
	return result



'''
Execute the real code
@param submission_id Submission ID
@param time_limit Time Limit
@param memory_limit Memory Limit
@param input_file In which file is stored the input
@return Result of executing code
'''
def ExecuteCode( submission_id, time_limit, memory_limit, input_file ):
	
	inputfile = open(input_file, "r")
	outputfile = open(path_runs + "/" + str(submission_id) + ".txt","w")
	
	arg = []
	arg.append(path_codes + str(submission_id))
	results = dict((getattr(Sandbox,'S_RESULT_%s' % i), i) for i in \
	('PD','OK','RF','RT','TL','ML','OL','AT','IE','BP'))
	sandbox_config = {
		'args': arg,
		'stdin': inputfile.fileno(),
		'stdout': outputfile.fileno(),
		'quota': dict(wallclock=int(time_limit),
			cpu=int(time_limit),
			memory=int(memory_limit)*1000000,
			disk=2000000)}


	sand_run = Sandbox(**sandbox_config)
	sand_run.policy = SelectiveOpenPolicy(sand_run,path_exceptions,[path_runs])
	sand_run.run()

	var_result = results.get(sand_run.result,'NA')
	return var_result


'''
Remove extra lines
@param s String
@return s without extra lines
'''
def remove_extra_lines(s):
	if len(s) == 0: 
		return s
	i = -1
	while s[i] == "\n":
		i -= 1
	if i < -1:
		s = s[:i+1]
	return s


'''
@param time_limit Time Limit
@param memory_limit Memory Limit
@param input_file Input File
@param output_file Output File
@param judge_file Useless
@param submission_id Submission ID
@return Result of running test case
'''
def RunTestCase( time_limit, memory_limit, input_file, output_file, judge_file, submission_id ):
	
	result = ExecuteCode(submission_id,time_limit,memory_limit,input_file)
	
	if result != "OK":
		return result
		
	correct_output = remove_extra_lines(file_read(output_file).replace("\r",""))
	
	user_output = remove_extra_lines(file_read(path_runs + "/" + str(submission_id) + ".txt").replace("\r",""))
	
	if correct_output != user_output:
		return "WA"
	return "OK"
