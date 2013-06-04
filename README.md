phabricator-hipchat-notifications
=================================

This is a simple library that can be added to Phabricator to get notifications in a HipChat room when the users interact with a revision.

For the communication with HipChat I used https://github.com/hipchat/hipchat-php library. The specific files for this library were added to this repository only be able to provide a fully functional library for [Phabricator](http://phabricator.org/)

To create this library I used the [official guide](http://www.phabricator.com/docs/phabricator/article/libphutil_Libraries_User_Guide.html) for creating libphutil libraries.

To install it follow the official guide [Installing Event Listeners](http://www.phabricator.com/docs/phabricator/article/Events_User_Guide_Installing_Event_Listeners.html)

After installing it, because of lack of better solutions for configuration and time, you need to edit the **src/events/PhabricatorHipChatEventListener.php** file to add your HipChat token and the room you want to send messages to.

	$token = 'your-hipchat-token'; # here you need to configure the HipChat token
    $hc = new HipChat($token);
    $hc->set_verify_ssl(false);

    $room = 'your-room'; # here you specify the room you want to post messages to
    $from = 'Phabricator'; # here you can configure the user that will apear as the sender of the message
    
**NOTE:** I know this is not pretty and very user friendly but it will have to do for now.

For instalation I used the standard paths, meaning that I placed the library next to phabricator. And since the configuration documentation for phabricator was confusing (at least for me), and maybe incomplete, I added in the file **<phabricator-folder>/conf/local/local.json**, the following:

	"events.listeners"     : ["PhabricatorHipChatEventListener"],
    "load-libraries"       : {
    "hipchat-notifications" : "phabricator-hipchat-notifications\/src\/"
    },
    
at the top. I know i should have used the **./bin/config set** but somehow i did not manage to send it JSON data. Maybe next time :(