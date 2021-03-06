<?php
require_once "app/console.php";
require_once "app/point.php";
require_once "app/element.php";
require_once "app/dungeon.php";
require_once "app/dice.php";

const dungeon_size = 100;
const dungeon_start_x = dungeon_size / 2;
const dungeon_start_y = dungeon_size / 2;
const dungeon_tile_size = 24;
const dungeon_with_grid = false;
const dungeon_as_ascii = false;

// init dungeon
$console = console::getInstance();
$dice = new dice();
$dungeon = new dungeon( dungeon_size, dungeon_size, dungeon_type_nothing );
$points[] = new point( dungeon_start_x, dungeon_start_y, heading_south_west );
$elements = [ element_stairs_start, element_passage_two, element_t_junction ];
$room_count = 6;

// set base dungeon elements
foreach ( $elements as $element )
{
    $point = array_pop( $points );

    $element = new element( $element, $point->get_direction() );

    if ( $dungeon->is_element_placable( $element, $point, true ) )
    {
        $points = $points + $dungeon->place_element( $element, $point );

        $console->log( $element->get_type() );
    }
}

// START generate dungeon
while ( $point = array_pop( $points ) )
{
    $passage_placeable_tries = 0;

    do // first step passage
    {
        $passage_placeable_tries++;

        $roll = $passage_placeable_tries >= 5 ? 2 : array_sum( $dice->roll( "1D12" ) );

        if ( $roll <= 3 ) $passage = new element( element_passage_one, $point->get_direction() );
        if ( $roll >= 4 && $roll <= 8 ) $passage = new element( element_passage_two, $point->get_direction() );
        if ( $roll >= 9 ) $passage = new element( element_passage_three, $point->get_direction() );

        $passage_placeable = $dungeon->is_element_placable( $passage, $point, true );

        if ( $passage_placeable )
        {
            // second step passage function
            $point_doors = $passage->roll_feature( $point );

            $room_count -= count( $point_doors );

            $point = $dungeon->place_element( $passage, $point )[ 0 ];

            $console->log( $passage->get_type() );

            // if passage has room(s), place it
            foreach ( $point_doors as $point_door )
            {
                $room_placeable_tries = 0;

                do
                {
                    $room_placeable_tries++;

                    $roll = array_sum( $dice->roll( "1D12" ) );

                    $room_direction = array_sum( $dice->roll( "1D12" ))  % 2 ? heading_north_east : heading_east_south;

                    if ( $roll <= 6 ) $room = new element( element_room_small, $room_direction ); // Normal Small
                    if ( $roll >= 7 && $roll <= 8 ) $room = new element( element_room_small, $room_direction ); // Hazard Small
                    if ( $roll >= 9 && $roll <= 10 ) $room = new element( element_room_large, $room_direction ); // Lair Large
                    if ( $roll >= 11 ) $room = new element( element_room_large, $room_direction ); // Quest Large

                    $room_placeable = $dungeon->is_element_placable( $room, $point_door, false );

                    if ($room_placeable)
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

                if ( $roll <= 3 ) $passage_end = new element( element_t_junction, $point->get_direction() ); // T-Junction
                if ( $roll >= 4 && $roll <= 8 ) $passage_end = new element( element_dead_end, $point->get_direction() ); // Dead End
                if ( $roll >= 9 && $roll <= 11 ) $passage_end = new element( element_corner_right, $point->get_direction() ); // Corner Right
                if ( $roll >= 12 && $roll <= 14 ) $passage_end = new element( element_t_junction, $point->get_direction() ); // T-Junction
                if ( $roll >= 15 && $roll <= 17 ) $passage_end = new element( element_corner_left, $point->get_direction() ); // Corner Left
                if ( $roll >= 18 && $roll <= 19 ) $passage_end = new element( element_stairs_down, $point->get_direction() ); // Stairs Down
                if ( $roll >= 20 && $roll <= 22 ) $passage_end = new element( element_stairs_out, $point->get_direction() ); // Stairs Out
                if ( $roll >= 23 && $roll <= 24 ) $passage_end = new element( element_t_junction, $point->get_direction() ); // Stairs Out

                $passage_end_placeable = $dungeon->is_element_placable( $passage_end, $point, ( $roll >= 4 && $roll <= 8 ) ? false : true );

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
// END generate dungeon

$tiles = $dungeon->get_tiles( dungeon_with_grid );
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Heroquest Dungeon Generator</title>
    <?php if ( dungeon_as_ascii ) { ?>
    <style>
        @font-face {
            font-family: ahq;
            src: url( font/ahq.ttf);
        }

        .dungeon {
            font-family: ahq, monospace;
            font-size: <?= dungeon_tile_size / 2 ?>px;
        }
    </style>
    <?php } else { ?>
    <style>
        @font-face {
            font-family: ahq;
            src: url( font/ahq.ttf);
        }

        :root {
            --tile-border: <?= dungeon_tile_size / 8 ?>px solid rgba( 0, 0, 0, 0.5 );
            --text-shadow: 0 0 <?= dungeon_tile_size / 8 ?>px black;
        }

        .dungeon {
            display: grid;
            grid-template-rows: repeat( <?= count( $tiles ) ?>, <?= dungeon_tile_size ?>px );
            grid-template-columns: repeat( <?= count( $tiles[ 0 ] ) ?>, <?= dungeon_tile_size ?>px );
        }

        .tile {
            position: relative;
            text-align: center;
        }

        .tile.border_top_left::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-top: var( --tile-border );
            border-left: var( --tile-border );
        }

        .tile.border_top::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-top: var( --tile-border );
        }

        .tile.border_top_right::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-top: var( --tile-border );
            border-right: var( --tile-border );
        }

        .tile.border_right::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-right: var( --tile-border );
        }

        .tile.border_bottom_right::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-bottom: var( --tile-border );
            border-right: var( --tile-border );
        }

        .tile.border_bottom::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-bottom: var( --tile-border );
        }

        .tile.border_bottom_left::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-bottom: var( --tile-border );
            border-left: var( --tile-border );
        }

        .tile.border_left::after {
            content: '';
            position: absolute;
            inset: 0 0 0 0;
            border-left: var( --tile-border );
        }

        .img {
            width: <?= dungeon_tile_size ?>px;
            height: <?= dungeon_tile_size ?>px;
        }

        .text {
            font-family: ahq, monospace;
            font-size: <?= dungeon_tile_size / 2 ?>px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-shadow: var( --text-shadow ), var( --text-shadow ), var( --text-shadow ), var( --text-shadow ), var( --text-shadow );
        }
    </style>
    <?php } ?>
</head>

<body>
<?php
// draw dungeon
if ( dungeon_as_ascii )
{
    echo '<p class="dungeon">';

    foreach ( $tiles as $row )
    {
        foreach ( $row as $cell )
        {
            echo $cell;
        }

        echo '<br />';
    }

    echo '</p>';
}
else
{
    echo '<div class="dungeon">';

    foreach ( $tiles as $row )
    {
        foreach ( $row as $cell )
        {
            $border = false;

            foreach ( segments as $type => $segment ) if ( in_array( $cell, $segment ) ) $border = $type;

            echo '<div class="tile' . ( $border ? ' ' . $border : '' ) . '">';

            if ( $cell == dungeon_type_nothing )
            {
                echo '<img alt="tile" class="img" src="img/tile_00.png">';
            }
            elseif ( $cell != '_' && !$border )
            {
                echo '<img alt="tile" class="img" src="img/tile_0' . random_int( 1, 8 ) . '.png">';
                echo '<span class="text">' . $cell . '</span>';
            }
            else
            {
                echo '<img alt="tile" class="img" src="img/tile_0' . random_int( 1, 8 ) . '.png">';
            }

            echo '</div>';
        }
    }

    echo '</div>';
}

// console output
if ( $log = $console->get_log() )
{
    echo '<script>';

    foreach ( $log as $entry )
    {
        echo $entry . "\n";
    }

    echo '</script>';
}
?>
</body>

</html>