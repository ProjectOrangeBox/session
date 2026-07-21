# Session

Thin wrapper around [aplus-framework/session](https://docs.aplus-framework.com/guides/libraries/session/index.html) exposed behind `SessionInterface`, so application code can depend on the interface instead of the concrete session implementation.

## Example

```php
use orange\session\Session;

$session = Session::getInstance($options); // options passed through to the aplus-framework session

$session->start();

$session->set('user_id', 42);
$userId = $session->get('user_id');
$session->has('user_id');
$session->remove('user_id');

// one-request-only values
$session->setFlash('notice', 'Saved!');
$notice = $session->getFlash('notice');

// values that expire after a ttl, independent of the request
$session->setTemp('otp', '123456', 300); // seconds
```

`Session` is a singleton (`getInstance()`); see `SessionInterface` for the full API, including `regenerateId()`, `destroy()`, and `gc()`.
