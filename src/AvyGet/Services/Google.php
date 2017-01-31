<?php namespace AvyGet\Services;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ProfileNotFound;
use AvyGet\Exceptions\ServiceFailed;
use Google_Client;
use Google_Http_Request;
use Google_Http_REST;
use Google_Service_Exception;
use Exception;

class Google extends AvatarServiceAbstract implements AvatarUrlInterface, AvatarServiceInterface {


    protected static $urlSizeParam = 'sz';

    protected $defaultFile1, $defaultFile2;

    /**
     * @var Google_Client
     */
    protected $google;

    /**
     * @param string $email
     * @param int    $size
     * @throws ServiceFailed
     */
    function __construct( $email, $size )
    {
        try
        {
            $this->google = new Google_Client;
        }

        catch( Exception $e ) {
            throw new ServiceFailed($e->getMessage());
        }

        parent::__construct($email, $size);
    }

    /**
     * @param      $email
     * @param null $size
     * @return string
     * @throws \Exception
     */
    public function getImageUrlByEmail( $email, $size )
    {
        $requestUrl = "https://picasaweb.google.com/data/entry/api/user/$email?alt=json";
        try{
            $response = $this->sendGoogleApiRequest($requestUrl);
        } catch( Google_Service_Exception $e){
            if( $e->getCode() === '404' ){
               throw new ProfileNotFound( 'Google profile not found' );
            }
        }

        if (empty($imageUrl = $response['entry']['gphoto$thumbnail']['$t'])) {
            throw new ImageNotFound('Google profile photo not found.');
        }

        $this->checkAgainstDefaultImages($imageUrl);

        $imageUrl = $this->morphImageUrlSize($imageUrl, $size);

        return $imageUrl;
    }

    /**
     * Construct and send authenticated API request to Google.
     *
     * @param $url
     * @return array
     */
    protected function sendGoogleApiRequest($url)
    {
        $rest = new Google_Http_REST();

        return $rest->execute(
            $this->google,
            new Google_Http_Request($url)
        );
    }

    /**
     * Check avatar image against Google default avatar images
     *
     * @param $url
     * @return void
     * @throws ImageNotFound
     */
    protected function checkAgainstDefaultImages($imageUrl)
    {
        $image1 = md5_file($imageUrl);
        $image2 = md5_file(__DIR__ . '/../Resources/Images/google_default_image_1.jpg');
        if($image1 == $image2){
            throw new ImageNotFound('Google profile photo is a default avatar');
        }

        $image2 = md5_file(__DIR__ . '/../Resources/Images/google_default_image_2.jpg');
        if($image1 == $image2){
            throw new ImageNotFound('Google profile photo is a default avatar');
        }
    }

}
 