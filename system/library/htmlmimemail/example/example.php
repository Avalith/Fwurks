<?php
/**
) o------------------------------------------------------------------------------o
* | This is HTMLMimeMail5. It is dual licensed as GPL and a commercial license.  |
* | If you use the code commercially (or if you don't want to be restricted by   |
* | the GPL license), you will need the commercial license. It's only £49 (GBP - |
* | roughly $98 depending on the exchange rate) and helps me out a lot. Thanks.  |
* o------------------------------------------------------------------------------o
*
* © Copyright 2005 Richard Heyes
*/

    require_once('../htmlMimeMail5.php');
    
    $mail = new htmlMimeMail5();

    /**
    * Set the from address
    */
    $mail->setFrom('Richard <richard@example.com>');
    
    /**
    * Set the subject
    */
    $mail->setSubject('Test email');
    
    /**
    * Set high priority
    */
    $mail->setPriority('high');

    /**
    * Set the text of the Email
    */
    $mail->setText('Sample text');
    
    /**
    * Set the HTML of the email
    */
    $mail->setHTML('<b>Sample HTML</b> <img src="background.gif">');
    
    /**
    * Add an embedded image
    */
    $mail->addEmbeddedImage(new fileEmbeddedImage('background.gif'));
    
    /**
    * Add an attachment
    */
    $mail->addAttachment(new fileAttachment('example.zip'));

    /**
    * Send the email
    */
    $mail->send(array('richard@example.com'));
?>