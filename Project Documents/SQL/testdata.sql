-- Test Data for DateRight

INSERT INTO Activities VALUES
	(1, "Going to the Movies", "If your significant other likes Rom-Coms, take him/her to see that movie he/she is always talking about.", 20.00, NULL, "Dallas, TX"),
	(2, "Romantic Dinner at Terrelli's", "This awesome Italian restaurant is perfect for date night because of its intimate atmosphere.", 60.00, NULL, "Dallas, TX"),
	(3, "Farmer's Market", "Farmer's markets are always a great date idea because who doesn't love fresh fruit?", 15.00, NULL, "Highland Park, TX"),
	(4, "Binge Watch Netflix", "If you and your significant other are close, a night in watching all 6 seasons of Lost would be fantastic!", 0.00, NULL, "Your Couch"),
	(5, "Cook them Dinner", "A romantic home cooked meal is always a good way to show how talented you are.", 40.00, NULL, "Your House"),
	(6, "Learn to Dance", "Go to a dance class and learn to salsa dance with your significant other!", 40.00, NULL, "Dallas, TX"),
	(7, "Workout Together", "Working out with your significant other can be a very fun and new thing for the two of you to do.", 10.00, NULL, "Local Gym"),
	(8, "Go Birdwatching", "There's something awfully romantic about being almost alone in the woods, quiet except for the sound of a far-off mating call.", 0.00, NULL, "Local Park"),
	(9, "Take a Brewery Tour", "Yes, you'll learn something, but you're also likely to get a token for a free beer for your troubles.", 20.00, NULL, "Local Brewery"),
	(10, "Visit a local Bookstore", "Your Kindle has tons of memory. but it sure isn't good at making them. Take a clue from the scene in Eternal Sunshine of the Spotless Mind when Joel (Jim Carrey) and Clementine (Kate Winslet) continually re-meet and have conversations among the shelves. Our suggestion: Start at the the travel section and get to know each other even better by talking dream trips.", 0.00, NULL, "Local Bookstore"),
	(11, "Spend the Night by the Fireside", "Start a fire, turn off the lights, and talk with your significant other until there's only embers", 0.00, NULL, "Your House"),
	(12, "Head to a Drive-In", "Grease's Sandy and Danny aren't the only ones who have a corner on the drive-in. Now that the kitschy location is making a comeback, you can find locations at driveinmovie.com. Grab some snacks, lean back, and enjoy.", 20.00, NULL, "Local Drive-In"),
	(13, "Go to a Museum", "Even if the new exibits do not seem that appealing to you, you and your significant other can still learn a thing or two and have some fun in the process", 20.00, NULL, "Local Museum"),
	(14, "Head to the Local Nursery", "More and more greenhouses are opening up cafes and gift shops, which make the destination more than just a place to buy mulch. Not only that, but walking around brightly colored flowers — especially when it's still freezing outside — can help influence your moods.", 20.00, NULL, "Local Nursery"),
	(15, "Go on a Picnic", "Nothing will make them happier than a blanket, basket, some yummy snacks, and a Kindle full of books they are ready to read", 20.00, NULL, "Local Park"),
	(16, "Explore a Winery", "The go-to activity was made famous in Sideways, but there's lots of not-so-obvious things to do at wineries. More and more are equipped with hiking trails, picnic areas, playgrounds, and even pools, turning vineyards into all-day destinations.", 20.00, NULL, "Local Winery"),
	(17, "Play a Childhood Boardgame", "Order pizza, uncork a bottle of wine, and play old-school card games or board games. It's a great way to unwind after a tough work week!", 0.00, NULL, "Your House"),
	(18, "Go to the Opera", "There's something so sexy about dressing in your absolute best as you watch a performance in a darkened theater. It was the turning point in Pretty Woman, and even if you don't understand exactly what's going on onstage, the music and the drama will sweep you both away.", 50.00, NULL, "Local Opera House")
	(19, "Get Caught in the Rain", "Forecast calls for cloudy skies? Great! Grab your guy and head outdoors. Getting caught in a spring shower (or snowstorm!) pretty much forces you to lock lips in a passionate embrace that's even more sexy than the one Allie (Rachel McAdams) and Noah (Ryan Gosling) have in The Notebook.", 0.00, NULL, "Outside"),
	(20, "Go Stargazing", "The best way to stargaze is to actually know what you're seeing, so download The Night Sky app. That way, you can actually be confident you're looking at the Big Dipper before you abandon all pretense and just make out.", 0.00, NULL, "Outside");

INSERT INTO Users VALUES
	(1, "johnnydoe", "John", "Doe", "jdoe@gmail.com", "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f", 1, 1, 1, "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f"),
	(2, "wizardking", "Harry", "Potter", "wizardking@hogwarts.com", "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f", 1, 1, 1, "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f"),
	(3, "originalplayer", "Abraham", "Lincoln", "oldabe@usa.gov", "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f", 1, 1, 1, "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f"),
	(4, "katniss", "Katniss", "Everdeen", "killyouall@hungergames.com", "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f", 1, 1, 1, "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f"),
	(5, "titmcgee", "Tits", "McGee", "tits@mcgee.com", "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f", 1, 1, 1, "92efeff958c88194ad0b983eed21ce90", "55c3b5386c486feb662a0785f340938f518d547f");

	-- THE PASSWORD & SECURITY ANSWERS UN-SALTED ARE: "password"

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
	(10, 'Classic'),
	(11, 'Activity');

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
	(10, 5),
	(11, 1),
	(11, 2), 
	(11, 3),
	(11, 4),
	(11, 5);

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
	(1, 5, 1 ,"Last night my I used the 'Casual Night In' dateplan and had a blast! It was very nice to cook a meal with my date, she loved it!", 1, 5, STR_TO_DATE('9/23/14 10:30 AM','%m/%d/%Y %h:%i %p')),
	(2, 2, 1, "Used the 'Casual Night In' dateplan and it wasn't great. I burned our food so we had to order Chinese :(", 1, 2, STR_TO_DATE('9/8/14 11:00 AM','%m/%d/%Y %h:%i %p')),
	(3, 4, 1, "Very nice DatePlan! A bit expensive though, but definitely worth it.", 2, 3, STR_TO_DATE('10/3/14 9:00 AM','%m/%d/%Y %h:%i %p')),
	(4, 1, 1, "Tried this last night. I dropped $60 last night on dinner and she did not instantly drop her panties. SadFace.", 3, 2, STR_TO_DATE('9/8/14 12:45 PM','%m/%d/%Y %h:%i %p'));

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


