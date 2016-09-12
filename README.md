# README #

Class for executing calls to cPanel UAPI

### What is this repository for? ###

* Just-do-the-job class for executing calls to cPanel UAPI
* Version 0.0.1

### How do I get set up? ###


```
#!php

require 'class.cpanel_uapi.php';
```

### Usage ###
See examples.php for full examples.

Initialize:

```
#!php

$host_IP_address = '1.0.0.127';
$cpanel_user = 'user';
$cpanel_pass = 'pass';

$cpanel_uapi = new CPanel_UAPI($host_IP_address, $cpanel_user, $cpanel_pass);
```

Make a call:

```
#!php

$module = 'Mysql'; // cPanel UAPI module
$function = 'get_privileges_on_database'; // cPanel UAPI function
$params = array(
	'user'     => 'dbuser',
	'database' => 'mydb',
	);
$test = $cpanel_uapi->execute($module, $function, $params);
```

### Who do I talk to? ###

* Repo owner or admin