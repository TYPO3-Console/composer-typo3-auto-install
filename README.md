# TYPO3 auto setup on composer installation

This is a composer package that aims to automate TYPO3 install steps
when installing TYPO3 with composer with the help of [TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console).

When executing a `composer install` TYPO3 setup is performed, asking for database and credentials.

Once the setup is performed, respectively a `LocalConfiguration.php` file is created for TYPO3,
consecutive `composer install` calls will not trigger the setup again.

It is possible to set the environment variable `TYPO3_IS_SET_UP=1` to disable the setup process.
This is helpful in continuous integration environments, where one would want to install dev
dependencies to perform automated testing, but a TYPO3 setup process is not required or wanted.

## Installation

It is recommended to add this package as dev dependency, so that it does not interfere
with build processes for deployment.

`composer require --dev typo3-console/typo3-auto-install`
