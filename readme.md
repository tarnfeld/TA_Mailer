TA_Mailer Class
================

This handy PHP class makes it really easy to implement templated emails. It's simple, just specify the template folder, add the template (or you could extend the class to set these as defaults – which is what I do) and then add some data! Here is a quick example below.

    $mailer = new TA_Mailer();
    $mailer->setTemplateDirectory('emails');
	$mailer->setTemplateFile('welcome.html');
    
    $data = array( "name" => "Tom", "awesomeness" => "100%" );
    $mailer->setData($data);
    
    $mailer->addTo("email@address.com");
    $mailer->setFrom("from@address.com");
    
    $mailer->send();

We are using an associative array to send in the tag => value to the mailer. Here is what **emails/welcome.html** would look like.

    Hey there {name}!
    
    Guess what, your submission to The Most Awesomeness Guy has been accepted and rated! We think your {awesomeness} awesome!

    Thanks.

There are plenty of applications for this, if you don't want to use the php mail() function you could extend the methods (as documented in the class file) and use it for Postmark.com which I've already done!

Enjoy, Tom.