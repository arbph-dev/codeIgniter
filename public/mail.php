<?php 
    $to = "arbph@sfr.fr"; 
    $subject = "Test mail PHP"; 
    $content = "The body/content of the Email";
    $headers = "From: Website <administrator@zealot.fr>\r\nReply-To: administrator@zealot.fr";

    if (mail($to, $subject, $content, $headers))
    echo "The email has been sent successfully!";
    else
    echo "Email did not leave correctly!";