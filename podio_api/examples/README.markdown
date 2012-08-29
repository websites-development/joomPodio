# Podio PHP Examples
This folder contains a small handful of examples for the Podio PHP library. They illustrate how you authenticate and make your first API call.

They are bare bones examples, one thing that is left as an exercise for the reader is the management of the access and refresh tokens. You will want to store these somewhere (e.g. in the session) so you don't have to do the authentication on every single page load.

To run these examples you must perform some quick configuration. Follow these steps:

* Go to https://podio.com/settings/api and create an API client id and client secret. The domain you use must be the domain you will be running these examples under (the domain "localhost" will always work).
* Create a copy of the file config.sample.php and call it config.php
* Open this new config.php and fill in your client id, client secret and your Podio username and password

You are now ready to run the examples. Open each example in your text editor for individual instructions.
