<?php namespace AvyGet\Services;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ServiceFailed;
use thomaswelton\GravatarLib\Gravatar as GravatarService;
use Exception;

class Gravatar extends AvatarServiceAbstract implements AvatarUrlInterface, AvatarServiceInterface {

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
     * @throws ServiceFailed
     */
    function __construct( $email, $size )
    {
        try {
            $this->gravatar = new GravatarService;
        }

        catch( Exception $e ) {
            throw new ServiceFailed($e->getMessage());
        }

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