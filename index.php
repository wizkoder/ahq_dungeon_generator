<?php
    require_once "app/point.php";
    require_once "app/element.php";
    require_once "app/dungeon.php";
    require_once "app/dice.php";

    // init dungeon
    $dice = new dice();
    $dungeon = new dungeon( 2000, 2000, dungeon_type_nothing );
    $points = [ new point( 999, 999, heading_south ) ]; // start point, every $dungeon->place_element call, returns the calculated point for the next element

    // base dungeon elements
    $points[ 0 ] = $dungeon->place_element( new element( element_stairs_start, $points[ 0 ]->get_direction() ), $points[ 0 ] );
    $points[ 0 ] = $dungeon->place_element( new element( element_passage_two, $points[ 0 ]->get_direction() ), $points[ 0 ] );

    $points[ 1 ] = unserialize( serialize( $points[ 0 ] ) );

    $points[ 0 ] = $dungeon->place_element( new element( element_t_junction_right, $points[ 0 ]->get_direction() ), $points[ 0 ] );
    $points[ 1 ] = $dungeon->place_element( new element( element_t_junction_left, $points[ 1 ]->get_direction() ), $points[ 1 ] );

    /**
    * !!! START generate dungeon !!!
    */
    $points_array_iterator = new ArrayIterator( $points );

    foreach ( $points_array_iterator as $point_idx => $point )
    {
        do
        {
            // first step passage
            $tmp_placeable = unserialize( serialize( $point ) );
            $tries = 0;

            do
            {
                $tries++;

                $roll = $tries > 3 ? 1 : array_sum( $dice->roll( "1D12" ) );

                if ($roll <= 3) $passage = new element( element_passage_one, $point->get_direction() );
                if ($roll >= 4 && $roll <= 8) $passage = new element( element_passage_two, $point->get_direction() );
                if ($roll >= 9) $passage = new element( element_passage_three, $point->get_direction() );
                
                // second step passage function
                $passage->roll_feature();

                // place passage
                $point = $dungeon->place_element( $passage, $point );

                // if passage has room(s), place it
                if ( $passage->get_feature() == "1 Door" || $passage->get_feature() == "2 Doors" )
                {
                    $direction = $passage->get_direction();
                    
                }
            }
            while ($point == $tmp_placeable);

            // third step passage end
            $tmp_placeable = unserialize( serialize( $point ) );
            $tries = 0;

            do
            {
                $tries++;

                $roll = $tries > 3 ? 6 : array_sum( $dice->roll( "2D12" ) );

                if ($roll <= 3) // T-Junction
                {
                    $passage_end = new element( element_t_junction_right, $point->get_direction() );

                    $new_point = unserialize( serialize( $point ) );

                    if ( $point->get_direction() == heading_north )
                    {
                        $new_point->set_direction( heading_south );
                    }

                    if ( $point->get_direction() == heading_east )
                    {
                        $new_point->set_direction( heading_west );
                    }

                    if ( $point->get_direction() == heading_south )
                    {
                        $new_point->set_direction( heading_north );
                    }

                    if ( $point->get_direction() == heading_west )
                    {
                        $new_point->set_direction( heading_east );
                    }

                    //$points_array_iterator[] = $new_point;
                }

                if ($roll >= 4 && $roll <= 8) // Dead End
                {
                    $passage_end = new element( element_dead_end, $point->get_direction() );
                }

                if ($roll >= 9 && $roll <= 11) // Corner Right
                {
                    $passage_end = new element( element_corner_right, $point->get_direction() );
                }

                if ($roll >= 12 && $roll <= 14) // T-Junction
                {
                    $passage_end = new element( element_t_junction_right, $point->get_direction() );

                    $new_point = unserialize( serialize( $point ) );

                    if ( $point->get_direction() == heading_north )
                    {
                        $new_point->set_direction( heading_south );
                    }

                    if ( $point->get_direction() == heading_east )
                    {
                        $new_point->set_direction( heading_west );
                    }

                    if ( $point->get_direction() == heading_south )
                    {
                        $new_point->set_direction( heading_north );
                    }

                    if ( $point->get_direction() == heading_west )
                    {
                        $new_point->set_direction( heading_east );
                    }

                    //$points_array_iterator[] = $new_point;
                }

                if ($roll >= 15 && $roll <= 17) // Corner Left
                {
                    $passage_end = new element( element_corner_left, $point->get_direction() );
                }

                if ($roll >= 18 && $roll <= 19) // Stairs Down
                {
                    $passage_end = new element( element_stairs_down, $point->get_direction() );
                }

                if ($roll >= 20 && $roll <= 22) // Stairs Out
                {
                    $passage_end = new element( element_stairs_out, $point->get_direction() );
                }

                $point = $dungeon->place_element( $passage_end, $point );
            }
            while ($point == $tmp_placeable);
        }
        while ($point->get_direction() != heading_end);
    }

    /**
    * !!! END generate dungeon !!!
    */

    // draw dungeon
    $dungeon->draw();
?>