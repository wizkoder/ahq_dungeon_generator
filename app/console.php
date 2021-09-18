<?php
class console
{
    // Properties
    public array $data;

    function __construct()
    {
        $this->data = array();
    }

    function log( $data )
    {
        array_push( $this->data, 'console.log('.json_encode( $data ).');' );
    }

    function get_log()
    {
        return $this->data;
    }
}
?>