Information
===========
GameQ 2 is a PHP program that allows you to query multiple types of multiplayer game servers at the same time.

GameQ v2 is based off of the original GameQ PHP program from http://gameq.sourceforge.net/.  That project was no longer being supported.

Requirements
============
* PHP 5.2 (Recommended 5.3+)
	* Bzip2 - http://www.php.net/manual/en/book.bzip2.php (A2S Compressed Responses)
	* Zlib - http://www.php.net/manual/en/book.zlib.php (AA3 Compressed Responses)
	
Example
=======
Usage & Examples: https://github.com/Austinb/GameQ/wiki/Usage-&-examples-v2

Quick and Dirty:

    $gq = new GameQ();
    $gq->addServer(array(
    	'id' => 'my_server',
    	'type' => 'css', // Counter-Strike: Source
    	'host' => '127.0.0.1:27015',
    ));
    
    $results = $gq->requestData(); // Returns an array of results
    
    print_r($results);

Want more? Check out the wiki page or /examples for more.

ChangeLog
=========
See CHANGELOG for specific list of changes

License
=======
See LICENSE for more information

Donations
=========
If you like this project and use it a lot please feel free to donate here: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VAU2KADATP5PU.
