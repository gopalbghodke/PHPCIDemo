# Design decision about Model<br>
<br>
Repository pattern has the advantage to create a bridge between models and controllers.<br>
But I (bruno martin) am not convienced yet to use this pattern because it increases the complexity of the application for a gain of flexibility (switching backend) that we actually may never use.<br>
My comment can be true for small/medium projects (< 10 developpers), but may not be true for big projects which need to be divided (code isolation) to reduce bugs and gain readability.
An external source that is explaining the useless of the pattern:
https://adelf.tech/2019/useless-eloquent-repositories

## Solution<br>
<br>
For small/medium application, I prefer to use an common parent Model to reuse many methods.<br>

## Reository pattern<br>
In case we still want to use the Repository pattern, here is an interesting link:<br>
https://www.larashout.com/how-to-use-repository-pattern-in-laravel
