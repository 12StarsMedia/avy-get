<?php namespace AvyGet\Services;

use AvyGet\Exceptions\ImageNotFound;
use thomaswelton\GravatarLib\Gravatar as GravatarService;

class Gravatar extends ProfilePhotoAbstract implements ImageUrlInterface {

    /**
     * @var string
     */
    protected static $urlSizeParam = 's';

    /**
     * @var GravatarService
     */
    protected $gravatar;

    /**
     * @param string $email
     * @param int    $size
     */
    function __construct( $email, $size )
    {
        $this->gravatar = new GravatarService;

        parent::__construct($email, $size);
    }

    /**
     * @param string $email
     * @param int    $size
     * @return string
     * @throws ImageNotFound
     */
    public function getImageUrlByEmail( $email, $size )
    {
        $imageUrl = $this->gravatar
            ->enableSecureImages()
            ->setDefaultImage('404')
            ->setAvatarSize($size)
            ->buildGravatarURL($email);

        $this->validateUrlResponse($imageUrl);

        return $imageUrl;
    }

}