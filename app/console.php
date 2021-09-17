<?php
class console
{
    function log( $data )
    {
        echo '<script>console.log('.json_encode( $data ).');</script>';
    }
}
?>