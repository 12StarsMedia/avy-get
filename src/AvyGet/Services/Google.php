<?php namespace AvyGet\Services;

use AvyGet\Exceptions\ImageNotFound;
use AvyGet\Exceptions\ProfileNotFound;
use Google_Auth_OAuth2;
use Google_Client;
use Google_Http_Request;
use Google_Http_REST;

class Google extends ProfilePhotoAbstract implements ImageUrlInterface {

    protected static $urlSizeParam = 'sz';

    /**
     * @var Google_Client
     */
    protected $google;

    /**
     * @var Google_Auth_OAuth2
     */
    protected $googleOAuth2;

    /**
     * @param string $email
     * @param int    $size
     */
    function __construct( $email, $size )
    {
        $this->google = new Google_Client;
        $this->google->setApplicationName(getenv('API.GOOGLE.APP_NAME'));
        $this->google->setDeveloperKey(getenv('API.GOOGLE.API_KEY'));

        $this->googleOAuth2 = new Google_Auth_OAuth2($this->google);

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
        $profileId = $this->getProfileIdByEmail($email);

        $requestUrl = "https://www.googleapis.com/plus/v1/people/$profileId?fields=image%2Furl";
        $response = $this->sendGoogleApiRequest($requestUrl);

        if ( empty($imageUrl = $response['image']['url']) ) {
            throw new ImageNotFound('Google profile photo not found.');
        }

        $imageUrl = $this->morphImageUrlSize($imageUrl, $size);

        return $imageUrl;
    }

    /**
     * Search Google Plus API for profile ID using email.
     *
     * @param string $email
     * @return string $profileId
     * @throws ProfileNotFound
     */
    protected function getProfileIdByEmail( $email )
    {
        $email = urlencode($email);
        $requestUrl = "https://www.googleapis.com/plus/v1/people?query=$email&fields=items%2Fid";
        $response = $this->sendGoogleApiRequest($requestUrl);

        if ( empty($response['items']) || empty($profileId = $response['items'][0]['id']) ) {
            throw new ProfileNotFound('Google profile not found.');
        }

        return $profileId;
    }

    /**
     * Construct and send authenticated API request to Google.
     *
     * @param $url
     * @return array
     */
    protected function sendGoogleApiRequest( $url )
    {
        $rest = new Google_Http_REST();

        return $rest->execute(
            $this->google,
            $this->googleOAuth2->authenticatedRequest(new Google_Http_Request($url))
        );
    }

}
 