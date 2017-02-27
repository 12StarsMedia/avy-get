# AvyGet Avatar Getter
This package attempts to get an avatar from an email address by checking the PicasaWeb and Gravatar APIs.

## Requirements
PHP 5.5 and later.

## Composer
Update your `composer.json`
```
{
  "require": {
    "12-stars-media/avy-get": "0.2.*"
  }
}
```
Followed by `composer install`.

## Getting Started

### To Use PicasaWeb API
Create app and API key to use Google+ API.
1) Set up a project on the [Google Developer Console](https://console.developers.google.com/project)
2) Enable Google+ API
3) Create a Public API Access key

Set environment variables so that AvyGet can authenticate you with Google's API.
- `API_GOOGLE_APP_NAME` (the name of your project or app)
- `API_GOOGLE_API_KEY` (your API key created in step 3 above)

### Basic Usage
Instantiate a new instance for each email you need a profile image for:

```
$avyGet = new AvyGet(
  'avyget@example.com', // Email address to find image for
  120                   // Desired image size in pixels (optional)
);

$avatar = $avyGet->url(); // Returns url for image
```

## Documentation

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

#### resize( int $size )

Returns modified AvyGet instance (i.e. it is chainable).

`$avyGet->resize(150)->url();`

#### url()

Returns the url instantiated with AvyGet or null if no avatar could be found for the provided email.

$avyGet->url();

#### urlArray( array $sizes )

Returns an indexed or associative array relative to what to provide it- replaces your size values with the appropriate URLs.

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

## License
AvyGet is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
