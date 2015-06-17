<?php namespace AvyGet;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ProfileNotFound;
use AvyGet\Exceptions\ServiceFailed;
use AvyGet\Services\AvatarUrlInterface;
use Exception;

class AvyGet {

    /**
     * Once an avatar is found, its service instance
     * is set here.
     *
     * @var AvatarUrlInterface
     */
    protected $avatarService;

    /**
     * Services to check for a profile photo.
     *
     * @var array
     */
    protected $services = [
        'AvyGet\Services\Google',
        'AvyGet\Services\Gravatar',
    ];

    /**
     * @param string    $email
     * @param integer   $size
     * @param array     $services
     * @throws Exception
     */
    function __construct( $email, $size = 200, array $services = [] )
    {
        if ( !empty($services) ) {
            $this->setServices($services);
        }

        $this->attemptServices($email, $size);
    }

    /**
     * Returns the stored URL is there is a working
     * avatar service available. Returns null if
     * there is no avatar service.
     *
     * @return $this
     */
    public function url()
    {
        if ( isset($this->avatarService) ) {
            return $this->avatarService->getUrl();
        }

        return null;
    }

    /**
     * Return array with size values replaced with appropriate
     * URLs for those size values.
     *
     * @param array $sizes
     * @return array
     */
    public function urlArray( array $sizes )
    {
        if ( isset($this->avatarService) ) {
            return $this->avatarService->getUrlArray($sizes);
        }

        return array_map(function()
        {
            return null;
        }, $sizes);
    }

    /**
     * Resize the size parameter on the URL if there
     * is a working avatarService. Otherwise do
     * nothing and return this class.
     *
     * @param $size
     * @return $this
     */
    public function resize( $size )
    {
        if ( isset($this->avatarService) ) {
            $this->avatarService->resizeUrl($size);
        }

        return $this;
    }

    /**
     * Loops through the $services array until it finds a
     * service which can return an avatar url.
     *
     * @param string  $email
     * @param integer $size
     * @return null
     * @throws Exception
     */
    protected function attemptServices( $email, $size )
    {
        // Check each source in order.
        foreach ( $this->services as $service )
        {
            try {
                $this->avatarService = new $service($email, $size);
            }

            catch ( ServiceFailed $e ) {
                continue;
            }

            catch ( ProfileNotFound $e ) {
                continue;
            }

            catch ( ImageNotFound $e ) {
                continue;
            }

            catch ( Exception $e ) {
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Set services for this class to access when
     * attempting to find a working image URL.
     *
     * Services must implement AvatarUrlInterface.
     *
     * @param array $services
     * @return $this
     */
    public function setServices( array $services )
    {
        $this->services = $services;

        return $this;
    }

}