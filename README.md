#Play This#

It is a system aiming to amuse and entertain people. Inspired by the old jukebox, a machine that reproduces music to insert coins, this system enables the choice of music that will be played on the environment.

##Technologies##

The system was developed based at:

* PHP 5.x
* JavaScript
* JQuery

##Usage##

You will need config your social tokens first at file _Config/config.php_

After that, you will need to put your mp3 songs at _Musics_ folder

And now you are able to start the system accessing your index.php file.

##How to ask an Music?##

The system reconize, until now, the same name of music, so you need to tweet

```
@tocaessa [artist name] - [music name]
```

or 

```
@tocaessa #play[music number]
```

To ask for a musica in the instagram, just, follow:

```
#tocaessa [artist name] - [music name]
```

or 

```
#tocaessa #play[music number]
```

#How the system works?#

The system is always looking for a specific folder, and indexes all the songs are there (_Musics_). These are the songs that can be chosen.

When starting the system, we take a look at the social networks, and check if there are any request. If so, we recorded this request in a queue in the order that was requested.

While the list is populated, we play.

At the end of this song, the next in line (if any) will be performed. 

If the list of requests finish, random songs will be performed until a new entry is identified.

#System Files#

The system does not use any database until now, just store the information in some files:

* _Data/in_last_id.txt_ is the control of the last id checked in instagram, cleaning this, the system will fill the row with all instagrams requests from the begining of requests;

* _Data/row.txt_ is the row of musics that will be played;
* _Data/tw_last_id.txt_ is the control of the last id checked in tweets, cleaning this, the system will fill the row with all tweets requests from the begining of requests;
* _Data/unidentified.txt_ stores all unidentifieds requests;


* _Logs/errors.txt_ store any system error;
* _Logs/in_requests.txt_ store all instragram requests;
* _Logs/tw_requests.txt_ store all twiter requests;

#Showing the list of musics#

The system can show the number indexer of all musics in the root file _list.php_ this list can show the links to tweet or insta an music request;