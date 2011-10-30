         lighter - a light php framework
        ---------------------------------
         Michel Begoc
        ---------------------------------
         2011-10-30
        ---------------------------------

* Introduction

    Lighter is a lightweight MVC PHP framework based on MongoDB.
    
    At this time, no other db is supported.

* Licence
    
    copyright (c) Michel Begoc
    distributed under MIT license. See lighter/docs/license.txt

* Installation

    1 ensure you have an environment set with Apache2, PHP 5.3 and MongoDB
    2 place your lighter framework in a folder correctly configured in your Apache
    3 edit the pmf/config/db.ini file and change the MongoDB connection info id needed
    4 create a folder for you app 
    5 edit the install.php file and change the constants for the values of your own
    6 execute the install.php file in command line
    7 if you wish, edit your apache config or add a .htaccess file to redirect the 
      requests to index.php so you don't need to specify the index.php file in the uri
    8 remove the install.php file

