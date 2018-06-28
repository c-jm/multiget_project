
# Overview:

This is a sample application, which is being used to gague my understanding of MultiPart Get requests using Range Headers.

The application must be able to download a file by using consecutive GET requests with Range Headers specified to a particular segment length. In my case, the segment length I was using was 4MB.

## Installation:

**NOTE**: This assumes you have [Composer](https://www.getcomposer.com) installed, if you do not please visit the link before continuing.

As this is a PHP application, it relies on a few different `composer` packages to be able to work correctly. 

After cloning, run `composer install` to install the direct dependencies for the application.

The two biggest dependecies used in the app are:

[Guzzle](www.github.com/guzzle/guzzle) an object oriented cURL wrapper written in PHP. This is primarily to make HTTP interactions alot easier to deal with.

[Measurements Bytes](https://github.com/arnovr/measurements-bytes) A package to help with working with byte sizes.

Once installed you can run the application by typing:

`php main.php`

within the application direcctory.

