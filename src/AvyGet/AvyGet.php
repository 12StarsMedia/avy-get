<?php namespace AvyGet;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ProfileNotFound;
use AvyGet\Services\ImageUrlInterface;
use Exception;

class AvyGet {

    /**
     * Once an avatar is found, its service instance
     * is set here.
     *
     * @var ImageUrlInterface
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
     * Email to return a photo URL for.
     *
     * @var string
     */
    protected $email;

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
     * @return $this
     */
    public function getUrl()
    {
        if ( isset($this->avatarService) ) {
            return $this->avatarService->getUrl();
        }

        return null;
    }

    public function getUrlArray( array $sizes )
    {
        if ( isset($this->avatarService) ) {
            return $this->avatarService->getUrlArray($sizes);
        }

        return [];
    }

    /**
     * @param $size
     * @return $this
     */
    public function resize( $size )
    {
        if ( isset($this->avatarService) ) {
            $this->avatarService->resize($size);
        }

        return $this;
    }

    /**
     * Set services for this class to access when
     * attempting to find a working image URL.
     *
     * Services must implement ImageUrlInterface.
     *
     * @param array $services
     * @return $this
     */
    public function setServices( array $services )
    {
        $this->services = $services;

        return $this;
    }

    /**
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

                return $this;
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

        return null;
    }

}