-- Test Data for DateRight

INSERT INTO Activities VALUES
	(1, "Going to the Movies", "If your significant other likes Rom-Coms, take him/her to see Saw 3! They'll love it!", 20.00, NULL, "Dallas, TX"),
	(2, "Romantic Dinner at Terrelli's", "This awesome Italian restaurant is perfect for date night because of its intimate atmosphere.", 60.00, NULL, "Dallas, TX"),
	(3, "Farmer's Market", "Farmer's markets are always a great date idea because who doesn't love fresh fruit?", 15.00, NULL, "Highland Park, TX"),
	(4, "Binge Watch Netflix", "If you and your significant other are close, a night in watching all 6 seasons of Lost would be fantastic!", 0.00, NULL, "Your couch"),
	(5, "Cook them Dinner", "A romantic home cooked meal is always a goto when it comes to trying to get in their pants ;)", 40.00, NULL, "Your house");

INSERT INTO Users VALUES
	(1, "John", "Doe", "jdoe@gmail.com", NULL, NULL, 1, 1),
	(2, "Harry", "Potter", "wizardking@hogwarts.com", NULL, NULL, 1, 1),
	(3, "Abraham", "Lincoln", "oldabe@usa.gov", NULL, NULL, 1, 1),
	(4, "Katniss", "Everdeen", "killyouall@hungergames.com", NULL, NULL, 1, 1),
	(5, "Tits", "McGee", "tits@mcgee.com", NULL, NULL, 1, 1);

INSERT INTO ActivitiesAttendees VALUES
	(1, 1, 1),
	(2, 2, 2),
	(3, 3, 3),
	(4, 4, 4),
	(5, 5, 5),
	(6, 1, 2),
	(7, 2, 3),
	(8, 3, 4),
	(9, 4, 5),
	(10, 5, 1);

INSERT INTO ActivityReviews VALUES
	(1, 5, 1, 1, "My girl loves Rom-Coms but hated Saw 3, but I loved her reactions to it so I gave it a 5 :)", 20.00, "Studio Movie Grill", 1, STR_TO_DATE('10/20/14 10:00 PM','%m/%d/%Y %h:%i %p')),
	(2, 5, 2, 2, "This restaurant it fantastic! Very lovely ambiance and the live piano player was so good!", 60.00, "Terrelli's restaurant", 1, STR_TO_DATE('10/10/14 8:00 PM','%m/%d/%Y %h:%i %p')),
	(3, 3, 3, 3, "The Farmer's market was just ok. My date didn't love the idea at first, but the homemade peanut butter really turned her around lol.", 15.00, "Dallas, TX", 1, STR_TO_DATE('10/15/14 10:00 AM','%m/%d/%Y %h:%i %p')),
	(4, 4, 4, 4, "Netflix was a perfect way to get even closer with my date. We barely Even watched the show! ;)", 0.00, "My bed", 1, STR_TO_DATE('10/20/14 11:00 PM','%m/%d/%Y %h:%i %p')),
	(5, 5, 5, 5, "I made an exceptional filet mignon with some twice baked potatoes and grilled asparagus. She immediately dropped her panties. 5/5", 40.00, "My Kitchen", 1, STR_TO_DATE('10/14/14 8:30 PM','%m/%d/%Y %h:%i %p'));

INSERT INTO DatePlans VALUES
	(1, "Casual Night In", 1, 1, 4, STR_TO_DATE('10/10/14 7:00 PM','%m/%d/%Y %h:%i %p'), "Have a romantic night in with some quality home cooking and then some netflix."),
	(2, "Classy Dinner and a Movie", 1, 2, 4, STR_TO_DATE('10/20/14 6:00 PM','%m/%d/%Y %h:%i %p'), "This fancy dateplan will make your significant other think they forgot your anneversary. Take them to a nice Italian restaurant and then to see that new romantic comedy they are always talking about."),
	(3, "Instant Pantie Dropper", 1, 5, 4, STR_TO_DATE('9/8/14 11:30 PM','%m/%d/%Y %h:%i %p'), "Take you S.O. to a nice Italian restaurant and then straight back for some 'Netflix' ;)");

INSERT INTO DateActivities VALUES
	(1, 1, 5),
	(2, 1, 4),
	(3, 2, 2),
	(4, 2, 1),
	(5, 3, 2),
	(6, 3, 4);

INSERT INTO Tags VALUES
	(1, 'Movies'),
	(2, 'Farmer''s Market'),
	(3, 'Italian'),
	(4, 'Cooking'),
	(5, 'TV'),
	(6, 'Restaurant'),
	(7, 'Home'),
	(8, 'Driving'),
	(9, 'Romantic'),
	(10, 'Classic');

INSERT INTO TaggedActivities VALUES
	(1, 1),
	(2, 3),
	(3, 2),
	(4, 5),
	(5, 4),
	(1, 4),
	(6, 2),
	(7, 4),
	(7, 5),
	(8, 1),
	(8, 2),
	(8, 3),
	(9, 2),
	(9, 5),
	(10, 1),
	(10, 2),
	(10, 5);

INSERT INTO Reports VALUES
	(1, 1, "The 'Instant Pantie Dropper' date plan did not instantly drop her panties. I'm suing you guys!!", 1),
	(2, 2, "Katniss Everdeen's review of watching Netflix was a bit too suggestive for me.", 1),
	(3, 3, "John Doe said that Saw 3 was a romantic comedy. It's not at all!!", 1);


INSERT INTO Favorites VALUES
	(1, 1, 1, 1),
	(2, 1, 2, 1),
	(3, 2, 3, 2),
	(4, 2, 4, 3),
	(5, 3, 5, 1),
	(6, 3, NULL, NULL),
	(7, 4, 4, NULL),
	(8, 4, 3, 3),
	(9, 5, 3, 2),
	(10, 5, NULL, 1);

INSERT INTO DatePlanReviews VALUES
	(1, 5, 1 ,"Last night my I used the 'Casual Night In' dateplan and had a blast! It was very nice to cook a meal with my date, she loved it!", 1, 5, 4, STR_TO_DATE('9/23/14 10:30 AM','%m/%d/%Y %h:%i %p')),
	(2, 2, 1, "Used the 'Casual Night In' dateplan and it wasn't great. I burned our food so we had to order Chinese :(", 1, 2, 4, STR_TO_DATE('9/8/14 11:00 AM','%m/%d/%Y %h:%i %p')),
	(3, 4, 1, "Very nice DatePlan! A bit expensive though, but definitely worth it.", 2, 3, 3, STR_TO_DATE('10/3/14 9:00 AM','%m/%d/%Y %h:%i %p')),
	(4, 1, 1, "Tried this last night. I dropped $60 last night on dinner and she did not instantly drop her panties. SadFace.", 3, 2, 3, STR_TO_DATE('9/8/14 12:45 PM','%m/%d/%Y %h:%i %p'));

INSERT INTO DatePlanAttendees VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 2, 3),
	(4, 2, 4),
	(5, 3, 5),
	(6, 3, 2);

INSERT INTO DatePlanActivityReviews VALUES
	(1, 2, 4),
	(2, 5, 1),
	(3, 1, 2),
	(4, 1, 3);


