<?php namespace AvyGet\Services;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ValidationError;

abstract class ProfilePhotoAbstract {

    /**
     * @var string
     */
    protected $url;

    /**
     * Key for size parameter returned on image url from API response.
     *
     * @var string
     */
    protected static $urlSizeParam;

    /**
     * @param string    $email
     * @param integer   $size
     */
    function __construct( $email, $size )
    {
        $this->url = $this->getImageUrlByEmail($email, $size);

        return $this;
    }

    /**
     * Returns image URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param array $sizes
     * @return array
     */
    public function getUrlArray( array $sizes )
    {
        foreach ( $sizes as $key => $size ) {
            $sizes[$key] = $this->resize($size)->getUrl();
        }

        return $sizes;
    }

    /**
     * Return image URL for specified image size.
     *
     * @param integer $size
     * @return string
     */
    public function resize( $size )
    {
        $this->url = $this->morphImageUrlSize($this->url, $size);

        return $this;
    }

    /**
     * Morph image URL to change the size parameter.
     *
     * @param string  $url
     * @param integer $size
     * @return string
     * @throws ValidationError
     */
    public function morphImageUrlSize( $url, $size )
    {
        $this->validateIsUrlSizeParamSet();
        $this->validateImageSizeIsInteger($size);

        $pattern = '/([\?|&]' . static::$urlSizeParam . '=)(\d+)/i';
        $replacement = '${1}' . $size;

        return preg_replace($pattern, $replacement, $url);
    }

    /**
     * Validates size field when passed into class.
     *
     * @param integer $size
     * @return bool
     * @throws ValidationError
     */
    protected function validateImageSizeIsInteger( $size )
    {
        if ( !is_integer($size) ) {
            throw new ValidationError('Image size must be an integer.');
        }

        return true;
    }

    /**
     * Make sure that the URL size parameter is found on
     * the extending service class.
     *
     * @return bool
     * @throws ValidationError
     */
    protected function validateIsUrlSizeParamSet()
    {
        if ( empty(static::$urlSizeParam) ) {
            throw new ValidationError('Image size URL parameter not found on service class.');
        }

        return true;
    }

    /**
     * Validates existence of image at URL.
     *
     * @param $url
     * @return bool
     * @throws ImageNotFound
     */
    protected function validateUrlResponse( $url )
    {
        $httpCode = $this->getHttpCodeFromUrl($url);

        if ( $httpCode < 200 || $httpCode >= 300 ) {
            throw new ImageNotFound('Profile photo not found at returned URL.');
        }

        return true;
    }

    /**
     * Returns the HTTP response code for the provided URL.
     *
     * @param string $url
     * @return int
     */
    protected function getHttpCodeFromUrl( $url )
    {
        $headers = get_headers($url);

        return (int) substr($headers[0], 9, 3);
    }







    /**
     * @param $email
     * @param $preset
     * @return string
     * @throws \Exception
     */
    public function getPresetAvatar( $email, $preset )
    {
        return $this->getAvatar($email, $this->getPreset($preset));
    }

    /**
     * @param       $email
     * @param array $presets
     * @return array
     */
    public function getPresetAvatars( $email, array $presets )
    {
        $avatars = [];

        foreach ( $presets as $preset ) {
            $avatars[$preset] = $this->getPresetAvatar($email, $preset);
        }

        return $avatars;
    }

    /**
     * @param $preset
     * @return mixed
     * @throws \Exception
     */
    public function getPreset( $preset )
    {
        if ( !array_key_exists($preset, $this->presets) ) {
            throw new \Exception('Preset not found.');
        }

        return $this->presets[$preset];
    }

    /**
     * @param $size
     * @return $this
     * @throws \Exception
     */
    public function setDefaultSize( $size )
    {
        if ( !is_integer($size) ) {
            throw new \Exception('Default size must be specified as an integer.');
        }
        $this->presets['default'] = $size;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setPreset( $key, $value )
    {
        $this->presets[$key] = $value;

        return $this;
    }

    /**
     * @param array $presets
     * @return $this
     */
    public function setPresetArray( array $presets )
    {
        foreach ( $presets as $key => $value ) {
            $this->setPreset($key, $value);
        }

        return $this;
    }

}
 