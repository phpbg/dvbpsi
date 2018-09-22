# PhpBg\DvbPsi

This is a pure PHP DVB PSI library, developed with performance in mind.
It tries to comply with "Digital Video Broadcasting (DVB) - Specification for Service Information (SI) in DVB systems" DVB PSI ETSI EN 300 468 V1.13.1

# Status

The initial goal is to provide parsing for following tables :
 * PAT: done
 * PMT: ongoing
 * NIT: not yet planned
 * BAT: not yet planned
 * SDT: not yet planned
 * EIT: mostly (missing some descriptors and proper encoding support)
 * TDT: done
 * TOT: todo
 * RST: not yet planned
 * ST: not yet planned
 * DIT: not yet planned
 * SIT: not yet planned
 

# Requirements

* PHP7+
* Composer

Those requirements can be installed on ubuntu 16.04:

    sudo apt install php7.0-cli composer

Or on windows:

1. http://php.net/downloads.php
2. https://getcomposer.org/download/


# Installation

Simply run:

    composer require phpbg/dvbpsi

# Examples

See `examples/` folder


# Development


You can install [xdebug](https://xdebug.org/download.php) for development purposes:

    sudo apt install php-xdebug


To run unit tests launch:

    php vendor/phpunit/phpunit/phpunit -c phpunit.xml
    
NB: to report code coverage add `--coverage-text` but keep in mind that launching with code coverage increase greatly the time required for tests to run (thus do not reflect real use case compute time)
