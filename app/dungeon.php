<?php

const dungeon_type_nothing = ".";

class dungeon
{
    // Properties
    public int $width;
    public int $height;
    public array $grid;

    function __construct( int $width, int $height )
    {
        $this->width = $width;
        $this->height = $height;

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                $this->grid[ $y ][ $x ] = dungeon_type_nothing;
            }
        }
    }

    // Methods
    function set_width( int $width )
    {
        $this->width = $width;
    }

    function get_width()
    {
        return $this->width;
    }

    function set_height( int $height )
    {
        $this->height = $height;
    }

    function get_height()
    {
        return $this->height;
    }

    function place_element( element $element, point $point )
    {
        $placeable = true;

        for ( $y = 0; $y < $element->get_height(); $y++ )
        {
            for ( $x = 0; $x < $element->get_width(); $x++ )
            {
                if ( $this->grid[ $y + $point->get_pos_y() ][ $x + $point->get_pos_x() ] != dungeon_type_nothing )
                {
                    $placeable = false;
                }
            }
        }

        if ( $placeable )
        {
            for ( $y = 0; $y < $element->get_height(); $y++ )
            {
                for ( $x = 0; $x < $element->get_width(); $x++ )
                {
                    $this->grid[ $y + $point->get_pos_y() ][ $x + $point->get_pos_x() ] = $element->get_tile( $x, $y );
                }
            }
            
            if ( $element->get_alignment() == element_horizontal )
            {
                $point->set_pos_x( $point->get_pos_x() + $element->get_width() );
            }
            else {
                $point->set_pos_y( $point->get_pos_y() + $element->get_height() );
            }
        }

        return $point;
    }

    function draw()
    {
        echo "<p  style='font-family: monospace, monospace'>";

        for ( $y = 0; $y < $this->height; $y++ )
        {
            for ( $x = 0; $x < $this->width; $x++ )
            {
                echo $this->grid[ $y ][ $x ];
            }

            echo "<br />";
        }

        echo "</p>";
    }
}
?>