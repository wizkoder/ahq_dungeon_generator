<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Heroquest Dungeon Generator</title>
</head>

<body>
<?php
    require_once "app/console.php";
    require_once "app/point.php";
    require_once "app/element.php";
    require_once "app/dungeon.php";
    require_once "app/dice.php";

    const dungeon_size = 50;
    const dungeon_start_x = dungeon_size / 2;
    const dungeon_start_y = dungeon_size / 2;
    const dungeon_tile_size = 30;
    const dungeon_with_grid = false;
    const dungeon_as_ascii = false;

    // init dungeon
    $console = new console();
    $dice = new dice();
    $dungeon = new dungeon( dungeon_size, dungeon_size, dungeon_type_nothing );
    $points[] = new point( dungeon_start_x, dungeon_start_y, heading_south_west );
    $elements = [ element_stairs_start, element_passage_two, element_t_junction ];
    $room_count = 6;

    // base dungeon elements
    foreach ($elements as $element)
    {
        $point = array_pop( $points );

        $element = new element( $element, $point->get_direction() );

        if ( $dungeon->is_element_placable( $element, $point, true ) )
        {
            $points = $points + $dungeon->place_element( $element, $point );

            $console->log( $element->get_type() );
        }
    }

    /**
    * !!! START generate dungeon !!!
    */

    while( $point = array_pop( $points ) )
    {
        $passage_placeable_tries = 0;

        do // first step passage
        {
            $passage_placeable_tries++;

            $roll = $passage_placeable_tries >= 5 ? 2 : array_sum( $dice->roll( "1D12" ) );

            if ($roll <= 3) $passage = new element( element_passage_one, $point->get_direction() );
            if ($roll >= 4 && $roll <= 8) $passage = new element( element_passage_two, $point->get_direction() );
            if ($roll >= 9) $passage = new element( element_passage_three, $point->get_direction() );

            $passage_placeable = $dungeon->is_element_placable( $passage, $point, true );

            if ( $passage_placeable )
            {
                // second step passage function
                $point_doors = $passage->roll_feature( $point );

                $room_count -= count( $point_doors );

                $point = $dungeon->place_element( $passage, $point )[0];

                $console->log( $passage->get_type() );

                // if passage has room(s), place it
                foreach ( $point_doors as $point_door )
                {
                    //$dungeon->place_point( $point_door );

                    $room_placeable_tries = 0;

                    do
                    {
                        $room_placeable_tries++;

                        $roll = array_sum( $dice->roll( "1D12" ) );

                        $room_direction = array_sum( $dice->roll( "1D12" ) ) % 2 ? heading_north_east : heading_east_south ;

                        if ( $roll <= 6 ) $room = new element( element_room_small, $room_direction ); // Normal Small
                        if ( $roll >= 7 && $roll <= 8 ) $room = new element( element_room_small, $room_direction ); // Hazard Small
                        if ( $roll >= 9 && $roll <= 10 ) $room = new element( element_room_large, $room_direction ); // Lair Large
                        if ( $roll >= 11 ) $room = new element( element_room_large, $room_direction ); // Quest Large
    
                        $room_placeable = $dungeon->is_element_placable( $room, $point_door, false );

                        if ( $room_placeable )
                        {
                            $dungeon->place_element( $room, $point_door );

                            $console->log( $room->get_type() );
                    }
                    } 
                    while ( !$room_placeable && $room_placeable_tries <= 5 );
                }

                $passage_end_placeable_tries = $room_count <= 0 ? 5 : 0;

                do // third step passage end
                {
                    $passage_end_placeable_tries++;

                    $roll = $passage_end_placeable_tries >= 5 ? 6 : array_sum( $dice->roll( "2D12" ) );
                    //$roll = 6;

                    if ($roll <= 3) $passage_end = new element( element_t_junction, $point->get_direction() ); // T-Junction
                    if ($roll >= 4 && $roll <= 8) $passage_end = new element( element_dead_end, $point->get_direction() ); // Dead End
                    if ($roll >= 9 && $roll <= 11) $passage_end = new element( element_corner_right, $point->get_direction() ); // Corner Right
                    if ($roll >= 12 && $roll <= 14) $passage_end = new element( element_t_junction, $point->get_direction() ); // T-Junction
                    if ($roll >= 15 && $roll <= 17) $passage_end = new element( element_corner_left, $point->get_direction() ); // Corner Left
                    if ($roll >= 18 && $roll <= 19) $passage_end = new element( element_stairs_down, $point->get_direction() ); // Stairs Down
                    if ($roll >= 20 && $roll <= 22) $passage_end = new element( element_stairs_out, $point->get_direction() ); // Stairs Out
                    if ($roll >= 23 && $roll <= 24) $passage_end = new element( element_t_junction, $point->get_direction() ); // Stairs Out

                    $passage_end_placeable = $dungeon->is_element_placable( $passage_end, $point, true );

                    if ( $passage_end_placeable )
                    {
                        foreach ( $dungeon->place_element( $passage_end, $point ) as $next_point )
                        {
                            array_push( $points, $next_point );
                        }

                        $console->log( $passage_end->get_type() );
                    }
                }
                while ( !$passage_end_placeable && $passage_end_placeable_tries <= 5 );
            }
        }
        while ( !$passage_placeable && $passage_placeable_tries <= 5 );
    }

    /**
    * !!! END generate dungeon !!!
    */

    // draw dungeon
    $dungeon->draw( dungeon_tile_size, dungeon_with_grid, dungeon_as_ascii );
?>
</body>

</html>