# TYPO3 auto setup on composer installation

This is a composer package that aims to automate TYPO3 install steps
when installing TYPO3 with composer with the help of [TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console).

When executing a `composer install` TYPO3 setup is performed, asking for database and credentials.

Once the setup is performed, respectively a `LocalConfiguration.php` file is created for TYPO3,
consecutive `composer install` calls will not trigger the setup again.

Since performing the setup requires user interaction, this process is also disabled when composer
is called with no interaction flag like `composer install --no-interaction`.
This is helpful in a continuous integration environment, where one would want to install dev
dependencies to perform automated testing, and the setup process would be an unwanted obstacle.

If for some reason the no interaction flag is no option, it is also possible to set the
environment variable `TYPO3_IS_SET_UP` to disable the setup process like `TYPO3_IS_SET_UP=1 composer install`.

## Installation

It is recommended to add this package as dev dependency, so that it does not interfere
with build processes for deployment.

`composer require --dev typo3-console/typo3-auto-install`
