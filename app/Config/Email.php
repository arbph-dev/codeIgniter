<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;





class Email extends BaseConfig
{
    public string $fromEmail  = 'fromEmail';
    public string $fromName   = 'fromName';
    public string $recipients = '';
    
    public string $userAgent = 'CodeIgniter'; // "user agent"
    
// The mail sending protocol: mail, sendmail, smtp
    //public string $protocol = 'mail';     
    public string $protocol = 'smtp';
    
// The server path to Sendmail.
    //public string $mailPath = '$mailPath';
    public string $mailPath = '';
// SMTP Server Hostname
    public string $SMTPHost = 'SMTPHost'; //NOT WORKS
  
    // SMTP authentication method to use: login, plain
    public string $SMTPAuthMethod = 'login'; //par defaut
// SMTP Username
    public string $SMTPUser = 'SMTPUser';
// SMTP Password
    public string $SMTPPass = 'SMTPPass';
// SMTP Port
    //public int $SMTPPort = 25;
    public int $SMTPPort = 587;//TLS    
// SMTP Timeout (in seconds)
    public int $SMTPTimeout = 5; //par défaut
// Enable persistent SMTP connections
    public bool $SMTPKeepAlive = false;//par défaut
//SMTP Encryption. '', 'tls' or 'ssl'.
//  'tls' will issue a STARTTLS command
//  'ssl' means implicit SSL. Connection on port 465 should set this to ''.
    public string $SMTPCrypto = 'tls';//par défaut
// Enable word-wrap
    public bool $wordWrap = true;//par défaut
// Character count to wrap at
    public int $wrapChars = 76;
// Type of mail, either 'text' or 'html'
    //public string $mailType = 'text';
    public string $mailType = 'html';
// Character set (utf-8, iso-8859-1, etc.)
    public string $charset = 'UTF-8';
// Whether to validate the email address
    public bool $validate = false;
// Email Priority. 1 = highest. 5 = lowest. 3 = normal
    public int $priority = 3;
// Newline character. (Use “\r\n” to comply with RFC 822)
    public string $CRLF = "\r\n";
// Newline character. (Use “\r\n” to comply with RFC 822)
    public string $newline = "\r\n";
// Enable BCC Batch Mode.
    public bool $BCCBatchMode = false;
// Number of emails in each BCC batch
    public int $BCCBatchSize = 200;
// Enable notify message from server
    public bool $DSN = false;
}
