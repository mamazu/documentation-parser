# Testing
Hello I am a normal markdown with **bold**, ~~underline~~ and _italic_ text and some links <a href="#">Some links</a>.

```xml
<a>
<b/>
</a>
```

```php
// Test
echo 'Hallo';

echo 2+2;
```

```php
<?php

use Mamazu\DocumentationParser\Parser\Block;
use Mamazu\DocumentationParser\Validator\XML\XMLValidValidator;

$validator = new XMLValidValidator();

$b = new Block('/tmp','test',1,'txt');
(new Mamazu\DocumentationParser\Validator\CompositeValidator([]))->validate($b);
```

And a little piece of valid yaml:
```yaml
hello:
    i_am_groot: true
    colors: ['red', 'green', 'blue']
```

And some bash:
```bash
if [ -z$HELLO ];
then
    echo "Hello"
fi
```

And some shell script:
```sh
if [-z$HELLO];
then
    echo "Hello"
fi
```
