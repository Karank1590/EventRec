# Cloud Computing Final Project
# Umang Patel - ujp2001, Karan Kaul - kak2210
# This script calls EventFul API and stores data in RDS 



import eventful
import MySQLdb

db = MySQLdb.connect(host="", # your host, usually localhost
                     user="", # your username
                      passwd="", # your password
                      db="") # name of the data base


cur = db.cursor()


api = eventful.API('EVENTFUL_KEY')



city=['New York','Boston','Los Angeles','Seattle','Chicago','Philadelphia','Houston','Phoenix','San Diego','Dallas']

for cty in range(len(city)):
	events = api.call('/events/search',l=city[cty],page_size=150,page_number=3,date='2014122400-2015122400')
	counter=0


	#print type(events['events']['event'])

	for event in events['events']['event']:
		counter+=1
		print str(counter)+"-----"+str(city[cty])
		#print event

		i_query="insert into eventful (id1,title,eurl,edesp,ecity,estate,ecounter,epostcode,evenueadd,evenueid,evenuename,evenueurl,elat,elng,estime) values ('"

		id1=event['id']	
		if(id1!=None):
			id1=str(id1.encode('utf-8'))
		#print "Event Id: %s" % (id1)
		i_query+=str(id1).replace("'", r"\'").replace('"', '\\"')+"','"


		title=event['title']
		if(title!=None):
			title=str(title.encode('utf-8'))
		#print "Event Name: %s" % (title)
		i_query+=str(title).replace("'", r"\'").replace('"', '\\"')+"','"


		eurl=event['url']
		if(eurl!=None):
			eurl=str(eurl.encode('utf-8'))
		#print "Event URL: %s" % (eurl)
		i_query+=str(eurl).replace("'", r"\'").replace('"', '\\"')+"','"



		edesp=event['description']
		if(edesp!=None):
			edesp=str(edesp.encode('utf-8'))
	
		#print "Event Description: %s" % (edesp)
		#print "------------------------"
		i_query+=str(edesp).replace("'", r"\'").replace('"', '\\"')+"','"


		ecity=event['city_name']
		if(ecity!=None):
			ecity=str(ecity.encode('utf-8'))
		#print "Event City: %s" % (ecity)
		i_query+=str(ecity).replace("'", r"\'").replace('"', '\\"')+"','"


		estate=event['region_name']
		if(estate!=None):
			estate=str(estate.encode('utf-8'))
		#print "Event State: %s" % (estate)
		i_query+=str(estate).replace("'", r"\'").replace('"', '\\"')+"','"


		ecountry=event['country_name']
		if(ecountry!=None):
			ecountry=str(ecountry.encode('utf-8'))
		#print "Event Country: %s" % (ecountry)
		i_query+=str(ecountry).replace("'", r"\'").replace('"', '\\"')+"','"


		epostcode=event['postal_code']
		if(epostcode!=None):
			epostcode=str(epostcode.encode('utf-8'))
		#print "Event Postal Code: %s" % (epostcode)
		i_query+=str(epostcode).replace("'", r"\'").replace('"', '\\"')+"','"


		evenueadd=event['venue_address']
		if(evenueadd!=None):
			evenueadd=str(evenueadd.encode('utf-8'))
		#print "Event Venue Address: %s" % (evenueadd)
		i_query+=str(evenueadd).replace("'", r"\'").replace('"', '\\"')+"','"


		evenueid=event['venue_id']
		if(evenueid!=None):
			evenueid=str(evenueid.encode('utf-8'))
		#print "Event Venue Id: %s" % (evenueid)
		i_query+=str(evenueid).replace("'", r"\'").replace('"', '\\"')+"','"


		evenuename=event['venue_name']
		if(evenuename!=None):
			evenuename=str(evenuename.encode('utf-8'))
		#print "Event Venue Name: %s" % (evenuename)
		i_query+=str(evenuename).replace("'", r"\'").replace('"', '\\"')+"','"


		evenueurl=event['venue_url']
		if(evenueurl!=None):
			evenueurl=str(evenueurl.encode('utf-8'))
		#print "Event Venue URL: %s" % (evenueurl)
		i_query+=str(evenueurl).replace("'", r"\'").replace('"', '\\"')+"','"

		elat=event['latitude']
		if(elat!=None):
			elat=str(elat.encode('utf-8'))
		#print "Event Latutude: %s" % (elat)
		i_query+=str(elat).replace("'", r"\'").replace('"', '\\"')+"','"

		elng=event['longitude']
		if(elng!=None):
			elng=str(elng.encode('utf-8'))
		#print "Event Longitude: %s" % (elang)
		i_query+=str(elng).replace("'", r"\'").replace('"', '\\"')+"','"


		estime=event['start_time']
		if(estime!=None):
			estime=str(estime.encode('utf-8'))
		#print "Event Start Time: %s" % (estime)
		i_query+=str(estime).replace("'", r"\'").replace('"', '\\"')+"')"

		#print i_query
		try:
			cur.execute(i_query)
			db.commit()
		except:
			print "Record Skipped ----->"
			continue 
		#print "------------------------"
		catgry=api.call('/events/get',id=id1)

		#print catgry

		#print len(catgry['categories']['category'])

	

		if (type(catgry['categories']['category'])==dict):
			c_query="insert into eventful_categories(eid1,cname,cid) values('"
			eid1=id1
			c_query+=eid1+"','"

			cname=catgry['categories']['category']['name']
			if(cname!=None):
				cname=str(cname.encode('utf-8'))
			c_query+=str(cname).replace("'", r"\'").replace('"', '\\"')+"','"

			cid=catgry['categories']['category']['id']
			if(cid!=None):
				cid=str(cid.encode('utf-8'))
			c_query+=str(cid).replace("'", r"\'").replace('"', '\\"')+"')"

			try:
				cur.execute(c_query)
				db.commit()
			except:
				print "Record Skipped----->"
				continue

		elif (type(catgry['categories']['category'])==list):
		

			for c1 in catgry['categories']['category']:

				c_query="insert into eventful_categories(eid1,cname,cid) values('"


				eid1=id1
				c_query+=eid1+"','"

				cname=c1['name']
				if(cname!=None):
					cname=str(cname.encode('utf-8'))
				c_query+=str(cname).replace("'", r"\'").replace('"', '\\"')+"','"


				cid=c1['id']
				if(cid!=None):
					cid=str(cid.encode('utf-8'))
				c_query+=str(cid).replace("'", r"\'").replace('"', '\\"')+"')"

				#print c_query
		
				try:
					cur.execute(c_query)
					db.commit()
				except:
					print "Record----->Skipped"
					continue
