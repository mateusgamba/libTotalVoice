# libTotalVoice
Api for communication with totalvoice SMS service.

## Example

```php
require_once('libTotalVoice.php');

// Instance with parameter token
$api = new libTotalVoice('token');

// Data
$cell = '48996447783';
$msg = 'Hello World';

// Send SMS
$return = $api->sendSMS($cell, $msg);

// Response
print_r($return);
