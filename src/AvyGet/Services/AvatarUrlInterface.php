<?php namespace AvyGet\Services;

interface AvatarUrlInterface {

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param array $sizes
     * @return array
     */
    public function getUrlArray( array $sizes );

    /**
     * @param integer $size
     * @return mixed
     */
    public function resizeUrl( $size );

} 