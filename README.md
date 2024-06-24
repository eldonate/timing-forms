# RaceTime - Race Registration Forms 

Here I develop my code to create a complete solution to manage running events.
My aim is to be able to create events, register users and get details about the upcomming races while you will be able to manage your registrations too

This to note:
There is a file named _race_registration_info.txt. This should contain the email body with the registration information about the race.
The name of the file has to include the race ID. For example, if your race ID is 2, the file should be named 2_race_registration_info.txt
Also the file has to be on the same location with the process_registration.php

I am not a developer and I am doing this to assist our local running community.
Please let me know if you want to contribute at dev@racetime.gr

Notes

-Setting "enabled" field on database, from 1 to 0, will hide the race from the registration form
-After completing registration, the participant will be redirected to the website page as it has been defined while adding an event.
-Also, after completing registration, there should be a txt file named "ID_race_registration_info.txt" which is the comfirmation email that the user will receive. ID is the race ID as it is on the database.
-There is a show_registrations.php_race_specific where we can set the race ID in order to show the participants. We can rename the file and provide this link to the organizers.
