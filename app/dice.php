<?php

class dice
{
    /**
     * Roll the dice
     *
     * Here's an example:<br />
     * <br />
     * $throws = roll("5d6");<br />
     * echo array_sum($throws)." [".implode(", ", $throws)."]";<br />
     * <br />
     * Output: 17 [1, 3, 6, 2, 5]
     *
     * @param string $notation - the dice notation e.g. 1d6, 4d6, etc.
     * @author sg
     * @return array - an array of integer values (the throws)
     */
    function roll( $notation )
    {
        $throws = array();

        if ( preg_match( "/(\d+)?[dDwW](\d+)+/", $notation, $dice ) )
        {
            for ( $throw = 0; $throw < ( !$dice[ 1 ] ? 1 : $dice[ 1 ] ); $throw++ )
            {
                array_push( $throws, random_int( 1, $dice[ 2 ] ) );
            }
        }

        return $throws;
    }
}

?>