-= Mootion =-

Mootion.com source code (as required by the Affero General Public License).

Video sharing site - digg-style based on meneame.net source code y I&ntilde;igo Gonzalez.

-= Quickstart =-

Prerequisites:

- PHP5 running as CGI
- MySQL database reachable from host.

Installation:

- Dump all distribution files to your server.
- Configure apache to serve the document root to the mootion.com directory
- Copy 127.0.0.1-local.php to servername-local.php (ie www.something.com -> www.something.com-local.php)
- Modify the servername-local.php file with your database server, db name, username, and password.
- Run the archives/mootion.sql script to populate the database.

Extras:

- Add your analytics tracking code to ads/googleanalytics.inc
- Add google adsense code to the PHP arrays inside the ads/adrotator-*.inc (empty by default)

If you want to add new video portal sources, the scraper is located at libs/videofarm.php.

All code is provided free of charge without warranty of any kind under the Affero GPL.
Remember: If you make any modification of the code, you must provide machine-readable online access to the source. Be kind and share.

Enjoy!