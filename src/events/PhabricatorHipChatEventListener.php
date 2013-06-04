<?php

/**
 * HipChat event listener.
 *
 * @group events
 */
final class PhabricatorHipChatEventListener extends PhutilEventListener
{
    // apparently this event is called multiple times because there are multiple emails sent so we will use this
    // as a flag to sent the message to HipChat only once
    public static $messageSent = false;

    public function register()
    {
        // When your listener is installed, its register() method will be called.
        // You should listen() to any events you are interested in here.

        $this->listen(PhabricatorEventType::TYPE_DIFFERENTIAL_WILLSENDMAIL);
    }

    public function handleEvent(PhutilEvent $event)
    {
        if (!self::$messageSent) {
            // When an event you have called listen() for in your register() method
            // occurs, this method will be invoked. You should respond to the event.

            // Instantiate the HipChat Class
            $token = 'your-hipchat-token';
            $hc = new HipChat($token);
            $hc->set_verify_ssl(false);

            $room = 'your-room';
            $from = 'Phabricator';
            $message = '';

            $phabricatorMetaMTAMailObj = $event->getValue('mail');

            $messageSubject = $phabricatorMetaMTAMailObj->getSubject();
            // here we get the body of the email that will be sent and try to extract some data from it to put in hipchat
            $theBody = $phabricatorMetaMTAMailObj->getBody();
            // we won't bother with fancy regular expressions for now. we will keep it simple and maybe not very reliable :)
            // but this, time will tell

            $theBody = explode(PHP_EOL, $theBody);

            // we add the first line of the email
            $messageDescription = $theBody[0];
            // now we search for the line with the text 'REVISION DETAIL'
            // because the next line will have the url to the revision and we want to add that in the message
            // this message is highly dependent on the format of the email, but hey, such is life
            $key = array_search('REVISION DETAIL', $theBody);
            // now we add the following line to the message. the one that has the url
            $messageUri = $theBody[($key + 1)];

            // after we got the data we build the message
            $message = '<a href="' . $messageUri . '">' . $messageSubject . '</a> : ' . $messageDescription;

            if (strlen($message) > 0) {
                // send a $message to the $room room from $from
                $hc->message_room($room, $from, $message);
            }

            // if we sent once the message then we don't want to send it anymore
            self::$messageSent = true;
        }
    }

}





