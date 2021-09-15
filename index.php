<?php
    // elements [ name, height, width, symbol ]
    const element_passage_one = [ "Passage One", 5, 2, "_" ];
    const element_passage_two = [ "Passage Two", 10, 2, "_" ];
    const element_passage_three = [ "Passage Three", 15, 2, "_" ];
    const element_t_junction = [ "T-Junction", 2, 2, "t" ];
    const element_dead_end = [ "Dead End", 5, 2, "e" ];
    const element_corner_right = [ "Corner Right", 2, 2, "r" ];
    const element_corner_left = [ "Corner Left", 2, 2, "l" ];
    const element_stairs_down = [ "Stairs", 2, 2, "d" ];
    const element_stairs_out = [ "Stairs", 2, 2, "u" ];
    const element_room_large = [ "Room", 10, 5, "-" ];
    const element_room_small = [ "Room", 5, 5, "-" ];

    const dungeon_size = 50;
    const dungeon_start_x = 25;
    const dungeon_start_y = 1;
    
    const heading_north = 0;
    const heading_east = 1;
    const heading_south = 2;
    const heading_west = 3;
    const heading_end = 4;

    require_once "app/point.php";
    require_once "app/element.php";
    require_once "app/dungeon.php";
    require_once "app/dice.php";

    $points = array();
    $dice = new dice();
    $dungeon = new dungeon( dungeon_size, dungeon_size, dungeon_type_nothing );

    $point = new point( dungeon_start_x, dungeon_start_y, heading_south );
    $element = new element( element_stairs_down, heading_south );
    $dungeon->place_element( $element, $point );

    $point = new point( dungeon_start_x, dungeon_start_y+2, heading_south );
    $element = new element( element_passage_two, heading_south );
    $dungeon->place_element( $element, $point );

    $point = new point( dungeon_start_x, dungeon_start_y+12, heading_south );
    $element = new element( element_t_junction, heading_south );
    $dungeon->place_element( $element, $point );
    
    $points[] = new point( dungeon_start_x-2, dungeon_start_y+12, heading_west );
    $points[] = new point( dungeon_start_x+2, dungeon_start_y+12, heading_east );

    $again = true;
    while ( $again )
    {
        $act_point = array_shift( $points );

        // first step passage
        
        $roll = rand( 1, 12 );
        
        if ( $roll <= 3 )
        {
            $passage = new element( element_passage_one, $act_point->get_direction() );
        }
        elseif ( $roll >= 4 && $roll <= 8 )
        {
            $passage = new element( element_passage_two, $act_point->get_direction() );
        }
        elseif ( $roll >= 9 )
        {
            $passage = new element( element_passage_three, $act_point->get_direction() );
        }

        $placeable = $dungeon->place_element( $passage, $act_point );

        if ( count( $points ) == 0 )
        {
            $again = false;
        }
    }
    
    

    $dungeon->draw();

?>