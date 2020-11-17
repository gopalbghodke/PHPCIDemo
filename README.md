# Introduction<br>
<br>
Sample library used as a tuturial to explain how to build Lincko library for modern PHP Frameworks.<br>
The main target is Laravel 6.x, but such library can also be imported inside Symphony or other framework that uses composer.<br>
<br>
<br>
## Documentation<br>
<br>
Please refer to the online wiki:<br>
[wiki](https://gitlab.com/brunoocto/sample/wikis/home)
<br>
<br>
## Instanciation<br>
It exists several ways to automatically instanciate a class when we need it, all of them have pros and cons, and some are more suitable than others depending on the situation.<br>
The [Controller](src/Controllers/SampleController.php) explains those differences:
 - Denpendency injection
 - Interface dependency injection
 - Facade
 - Maker
 
<br>
<br>
## Installation<br>
<br>
In your framework root directory, open composer.json and add the following repository:<br>
```json
    [...]
    "repositories": [
        [...]
        {
            "type": "vcs",
            "url":  "git@gitlab.com:brunoocto/sample.git"
        }
        [...]
    ]
    [...]
```
<br>
<br>
Then import the library via composer:<br>
```console
me@dev:# composer require brunoocto/sample:"~1.0"
```
<br>
<br>
## Tests<br>
<br>
Run all Feature tests, Unit tests, and generate a Coverage report:<br>
```console
me@dev:# phpunit
```
If you want to launch a single method for debuging:
```console
me@dev# phpunit ./tests/Unit/Models/SampleTest.php --filter '^.*::testCreateASample( .*)?$' --no-coverage
```
<br>
