G2 Mvc Framework PHP
=========
NOTE: 
This is my first Project i make publically available. Any criticism is welcome. 
Suggestions on improving my documenting or even helping me start documenting would be nice

Description:
a small MVC framework. that also has an implementation of Twig templating engine as a controller

This framework spiralled into something bigger.

Features
=========
a router Class that is used to route all request through controllers based in packages
controller routing can also happen on a package level

a Twig View system that can be initialized inside controllers actions

a ini based config system. to configure connections to databases, twig cache locations twig view locations etc

Integrated ORM using redbeansPHP. Still looking for a good wrapper database class that atleast has a couple of ease of use functions

an library autoloader base on the Zend Autoloader style

Using a library embedded into the framework. the only file that needs to be included is the Mvc_Router.php file in the framework route.

HOW TO:
I have included a sample project into the repo. So feel free to look at it.

@todo: improve this readme
