# AvyGet from [12 Stars Media](http://www.12starsmedia.com)
This package attempts to get an avatar from an email address by checking Google+ and Gravatar.

## Setup

### Require AvyGet using composer:

`composer require 12-stars-media/avy-get`

### Add app name and API key to use Google+ API.

1) Set up a project on the [Google Developer Console](https://console.developers.google.com/project)
2) Enable Google+ API
3) Create a Public API Access key

### Environment Variables

- `API.GOOGLE.APP_NAME` (the name of your project or app)
- `API.GOOGLE.API_KEY` (your API key created in step 3 above)

## Basic Usage
Instantiate a new instance for each email you need a profile image for:

```
$avyGet = new AvyGet(
  'avyget@example.com', // Email address to find image for
  120                   // Desired image size in pixels
);

$avatar = $avyGet->url(); // Returns url for image
```

## AvyGet API

### Instantiate AvyGet

`new AvyGet( string $email [, int $size = 200 ] [, array $services = [] ] );`

Only the email is required, but you can request an avatar in a specific size and also modify what services are used to find avatars.

### Avatar Source Services

Default services and order of preference is:

```
protected $services = [
    'AvyGet\Services\Google',
    'AvyGet\Services\Gravatar',
];
```

More services can be created by extending `AvyGet\Services\ProfilePhotoAbstract` and implementing the `AvyGet\Services\ImageUrlInterface`.

### Methods

#### `resize( int $size )`

Returns modified AvyGet instance (i.e. it is chainable).

`$avyGet->resize(150)->url();`

#### `url()`

Returns the url instantiated with AvyGet or null if no avatar could be found for the provided email.

#### `urlArray( array $sizes )`

Returns an indexed or associative array based on your input.

```
$avyGet->urlArray([
  32,
  128,
  512
]);

/**
 * [
 *   'http://www.example.com/image?size=32',
 *   'http://www.example.com/image?size=128',
 *   'http://www.example.com/image?size=512',
 * ]
 */
```

```
$avyGet->urlArray([
  'small'  => 32,
  'medium' => 128,
  'large'  => 512,
]);

/**
 * [
 *   'small'  => 'http://www.example.com/image?size=32',
 *   'medium' => 'http://www.example.com/image?size=128',
 *   'large'  => 'http://www.example.com/image?size=512',
 * ]
 */
```
