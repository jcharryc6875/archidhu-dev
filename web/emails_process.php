<?php

set_time_limit(3000);
$hostname = '{someoutlookemail.outlook.com:993/imap/ssl}INBOX';
$username = 'someoutlookemail@outlook.com';
$password = 'apass';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');


$max_emails = 16;

/* if emails are returned, cycle through each... */
if($emails) {

    $count = 1;

    /* begin output var */
    $output = '';

    /* put the newest emails on top */
    rsort($emails);

    /* for every email... */
    foreach($emails as $email_number) {

        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox,$email_number,0);
        $message = imap_fetchbody($inbox,$email_number,1.1);
        $structure = imap_fetchstructure($inbox,$email_number);

         $attachments = array();

        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                    /* 3 = BASE64 encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        /* iterate through each attachment and save it */
        foreach($attachments as $attachment)
        {
            if($attachment['is_attachment'] == 1)
            {
                $filename = $attachment['name'];
                if(empty($filename)) $filename = $attachment['filename'];

                if(empty($filename)) $filename = time() . ".dat";

                /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */
                $fp = fopen("./" . $email_number . "-" . $filename, "w+");
                fwrite($fp, $attachment['attachment']);
                fclose($fp);
            }

        }

        if($count++ >= $max_emails) break;


        /* output the email header information */
        $output.= '<p><div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
        $output.= '<p>Subject: <span class="subject">'.$overview[0]->subject.'</span> ';
        $output.= '<p>From: <span class="from">'.$overview[0]->from.'</span>';
        $output.= '<p>Date: <span class="date">on '.$overview[0]->date.'</span>';
        $output.= '</div>';

        /* output the email body */
        $output.= '<p>Message:<div class="body">'.$message.'';
        $output.= '<p>attachment:'.$filename.'';
        $output.= '<table><tr><hr size="1" width="100%%" noshade color="black" ></tr></table>';


    }

    echo $output;
}

/* close the connection */
imap_close($inbox);
?>
