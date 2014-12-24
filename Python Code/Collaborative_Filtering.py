# Cloud Computing Final Project
# Umang Patel - ujp2001, Karan Kaul - kak2210
# This script calls RDS and creates marix on which SVD is done and pushes the SVD result to RDS 


import MySQLdb
import numpy
import math


def matrix_factorization(R, P, Q, K, steps=5000, alpha=0.0002, beta=0.02):
    Q = Q.T
    for step in xrange(steps):
        for i in xrange(len(R)):
            for j in xrange(len(R[i])):
                if R[i][j] > 0:
                    eij = R[i][j] - numpy.dot(P[i,:],Q[:,j])
                    for k in xrange(K):
                        P[i][k] = P[i][k] + alpha * (2 * eij * Q[k][j] - beta * P[i][k])
                        Q[k][j] = Q[k][j] + alpha * (2 * eij * P[i][k] - beta * Q[k][j])
        eR = numpy.dot(P,Q)
        e = 0
        for i in xrange(len(R)):
            for j in xrange(len(R[i])):
                if R[i][j] > 0:
                    e = e + pow(R[i][j] - numpy.dot(P[i,:],Q[:,j]), 2)
                    for k in xrange(K):
                        e = e + (beta/2) * (pow(P[i][k],2) + pow(Q[k][j],2))
        if e < 0.001:
            break
    return P, Q.T


def sql():

	fid_list=[]
	score=[]
	catgry=[]
	tempscore=[]

	db = MySQLdb.connect(host="",user="",passwd="",db="") 
	b=cur.execute("select * from prior_rating")
	rows = cur.fetchall()
	for row in rows:

		srow=str(row)
		slist=srow.replace("(","").replace(")","").split(",")
		srating=int(slist[2].strip().replace("L",""))

		fid=slist[0].replace("'","").replace("'","")
		tctgry=slist[1].replace("'","").replace("'","").strip()

		if tctgry not in catgry:
			catgry.append(tctgry)

		

		if fid not in fid_list:
			fid_list.append(str(fid))

			if tempscore!=[]:
				score.append(tempscore)

			tempscore=[]
			tempscore.append(srating)	
		else:
			tempscore.append(srating)		
	

	score.append(tempscore)
	#print len(tempscore)
	#print len(score[0])
	#print catgry
	#print score	

	#print fid_list

	reco(score,catgry,fid_list,cur,db)

def reco(Rarray,catgry,fid_list,cur,db):

	print "Starting SVD"

	R = numpy.array(Rarray)

	N = len(R)
	M = len(R[0])
	K = 2

	P = numpy.random.rand(N,K)
	Q = numpy.random.rand(M,K)

	nP, nQ = matrix_factorization(R, P, Q, K)
	nR = numpy.ceil(numpy.dot(nP, nQ.T)).astype(int)

	print "Completed SVD"

	#print catgry

	#print fid_list

	nRlist=nR.tolist()

	for i in range(len(nRlist)):
		for j in range(len(nRlist[i])):
			query="replace INTO post_rating(fid,cid,rating) values(\'"+str(fid_list[i])+"\',\'"+str(catgry[j])+"\',"+str(nRlist[i][j])+")"
			#print query
			cur.execute(query)
			db.commit()



if __name__ == '__main__':
	sql()