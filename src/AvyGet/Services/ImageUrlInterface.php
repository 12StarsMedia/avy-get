<?php namespace AvyGet\Services;

interface ImageUrlInterface {

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param integer $size
     * @return mixed
     */
    public function resize( $size );

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