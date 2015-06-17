<?php namespace AvyGet\Services;

interface AvatarServiceInterface {

    /**
     * Performs necessary queries and returns an image URL
     * or throws an exception.
     *
     * @param string $email
     * @param integer $size
     * @return string $url
     */
    public function getImageUrlByEmail( $email, $size );

    /**
     * Morphs a URL returned by getImageUrl.
     *
     * @param string $url
     * @param integer $size
     * @return string $url
     */
    public function morphImageUrlSize( $url, $size );

}
 