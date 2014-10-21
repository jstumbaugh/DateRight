// Test Data for DateRight

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




