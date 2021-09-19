<?php
class console
{
    // Properties
    private static $instance;
    private array $data;

    private function __construct()
    {
        $this->data = array();
    }

    private function __clone( )
    {
    }

    public static function getInstance( )
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self( );
        }

        return self::$instance;
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