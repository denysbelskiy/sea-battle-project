<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'user_id', 'data'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function joinPlayerTwo($user2_id) {
        // store user_in in the table games field user2_id
        $this->user2_id = $user2_id;
        $this->save();
    }

    public function initGame() {
        
        $data = array(
            'configuration' => array(
                'board' => array(
                    'x_length' => 10,
                    'y_length' => 10
                )
            ),
            'status' => array(
                'finished' => false,
                'winner' => null,
                'shooter' => null
            ),
            'shots' => array(
                1 => array(

                ),
                2 => array(

                )
            ),
            'targets' => array(
                1 => array(
                    array(
                        'locations' => array(
                            array('x' => 1, 'y' => 1, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                    array(
                        'locations' => array(
                            array('x' => 4, 'y' => 3, 'hit' => false),
                            array('x' => 5, 'y' => 3, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                    array(
                        'locations' => array(
                            array('x' => 7, 'y' => 5, 'hit' => false),
                            array('x' => 7, 'y' => 6, 'hit' => false),
                            array('x' => 7, 'y' => 7, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                ),
                2 => array(
                    array(
                        'locations' => array(
                            array('x' => 6, 'y' => 6, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                    array(
                        'locations' => array(
                            array('x' => 4, 'y' => 6, 'hit' => false),
                            array('x' => 5, 'y' => 6, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                    array(
                        'locations' => array(
                            array('x' => 9, 'y' => 5, 'hit' => false),
                            array('x' => 9, 'y' => 6, 'hit' => false),
                            array('x' => 9, 'y' => 7, 'hit' => false),
                        ),
                        'destroyed' => false
                    ),
                )
            )
        );

        $this->data = json_encode($data);
        $this->save();
    }

    public function prepareData() {
       
        $this->data = json_decode($this->data);
    
    }

    public function shoot($y, $x) {
       
       $this->prepareData();
       $hit_detection = false;
       $player = 0;
       $gameover = false;

       if($this->user_id == auth()->user()->id) {
            $player = 1;
       } elseif($this->user2_id == auth()->user()->id) {
            $player = 2;
       }  

       // check if we hit target
       $targets = $this->data->targets->{$player};
       foreach($targets as $target){
            $coordinates = $target->locations;
            foreach($coordinates as $coordinate) {
                if($coordinate->x == $x && $coordinate->y == $y){
                    $hit_detection = true;
                    // TODO: also set hit inside ship to true
                    break 2;
                }
            }

       }

       // TODO: check if gameover (no more ships)
       // -> data:status:finished = true
       // -> data:status:winner = player 1 or player 2

       $this->data->shots->{$player}[] = array(
            'x' => $x,
            'y' => $y,
            'hit' => $hit_detection
       ); 

       $this->data = json_encode($this->data );
       $this->save();

       $response = array(
            'x' => $x,
            'y' => $y,
            'hit' => $hit_detection,
            'gameover' => $gameover
       );

       return $response;
       
    }
}


