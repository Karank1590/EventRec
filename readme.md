EventRec
========

Authors
-------
Karan Kaul- kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu


GitHub URL
----------
https://github.com/17patelumang/EventRec


Youtube URL
-----------
https://www.youtube.com/watch?v=X8BP-KTjBdo&feature=youtu.be


Description
-----------
A smart Event Recommendation System, suggests events taking into consideration a user's social media interaction. A user logs in using their Facebook credentials, information about a person’s interests is collected and we utilize this data to recommend events. As far as events go, we collect real world events occurring in various cities from popular event platforms, Eventful and Seatgeek. All this is usable through a web application built in PHP.

Components & Flow
-----------------
1. Initially the user accesses the application using their Facebook credentials and allows
the application to access information about the user.
2. Collect Books, Events, Games, Interests, Music, Movies and Pages liked by the user
and store this information in a database.
3. This data is then mapped into corresponding predefined categories for each entry.
4. Based on the number of entries in each category, the prior rating for each category is
calculated.
5. For unrated categories, incremental SVD is performed to provide post ratings.
6. The highest rated categories in the post ratings are the suggested categories.
7. Clicking on one of these categories presents the various events within that category.
8. Clicking on an event presents all information about that event to the user.
9. The user may share the event on facebook, get information about the venue from yelp,
see the location of the event on Google Maps and see various other users attending
the event.
10. If the user clicks on the Attend button, he gets added to list of users attending the
event.

Data Collection
---------------

We collected the data from SeatGeek and EventFul. We collected 12838 events happening in 10 cities namely ; 'New York', 'Boston', 'Los Angeles', 'Seattle', 'Chicago', 'Philadelphia', 'Houston', 'Phoenix', 'San Diego', 'Dallas' . 

Recommendation System
---------------------

So in recommendation system after we collect Facebook data from given user we map it to 29 categories which we got from EventFul API. We also map SeatGeek events to categories from EventFul API . After we do the mapping, we calculate the frequency  in each category for given user and convert it to score of  1-5 integers. Then after getting the score we create the user*category score matrix and feed it to recommendation system. The gives us a new matrix ( user * categories) having new scores and we display the categories for user based on score.

We also take user feedback of going to event by taking weighted average of the of the score from recommendation system and the score we get after calculating the frequency of event for different categories.

Recommendation system basically does standard SVD. We solve it as an optimisation problem where we try to reduce the mean square  error between true matrix and estimated matrix. Since the optimization function is convex  we use gradient  descent method  to solve the optimisation problem.


Technologies Used
-----------------
1. PHP
2. Java
3. Python
4. JQuery
5. CSS
6. Twitter Bootstrap
7. MySQL
8. Facebook Graph API
9. SeatGeek API
10. EventFul API
11. Yelp API
12. Google Maps


Libraries Used
--------------
1. Amazon AWS PHP SDK
2. Facebook PHP SDK
3. Python eventful, MySQLdb, numpy, math
