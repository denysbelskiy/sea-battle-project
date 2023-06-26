<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
                'shooter' => 1
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
                            array('x' => 1, 'y' => 9, 'hit' => false),
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
       $player = 0; $enemy = 0;
       $gameover = false;
       $isDestroyed = false;
       $areTargetsDestroyed = false;
       $winner = $this->data->status->winner;

       if($this->user_id == auth()->user()->id) {
            $enemy = 2;
            $player = 1;
       } elseif($this->user2_id == auth()->user()->id) {
            $enemy = 1;
            $player = 2;
       }  

       // check if we hit target
       $targets = $this->data->targets->{$enemy};
       $hitted_target = null;
       foreach($targets as $target){
            $coordinates = $target->locations;
            foreach($coordinates as $coordinate) {
                if($coordinate->x == $x && $coordinate->y == $y){
                    $hit_detection = true;
                    $coordinate->hit = $hit_detection;
                    $hitted_target = $target;
                    break 2;
                }

            }

       }

       if($hit_detection === true){
        $coordinates = $hitted_target->locations;
        foreach($coordinates as $coordinate) {
            
            if($coordinate->hit === true) {
                $isDestroyed = true;
            }
            else {
                $isDestroyed = false;
                break;
            }
        }

        $hitted_target->destroyed = $isDestroyed;

        if($isDestroyed === true) {
            foreach($targets as $target) {
                if($target->destroyed === true) {
                    $areTargetsDestroyed = true;
                }
                else {
                    $areTargetsDestroyed = false;
                    break;
                }
            }
            
        }

        //save data if game over and who is winner
        if($areTargetsDestroyed === true) {
            $this->data->status->finished = true;
            $this->data->status->winner = $player;
        }
       

       }

       $this->data->shots->{$player}[] = array(
            'x' => $x,
            'y' => $y,
            'hit' => $hit_detection
       ); 

       $winners_name = '';
       if($this->data->status->finished) {
        if($this->data->status->winner == 1) {
            $user = User::find($this->user_id);
        } else {
            $user = User::find($this->user2_id);
        }

        $winners_name = $user->name;
       }

       // change status->shooter to the other player
       $this->data->status->shooter = $enemy;

       $this->data = json_encode($this->data );
       $this->save();
       $response = array(
            'x' => $x,
            'y' => $y,
            'hit' => $hit_detection,
            'gameover' => $gameover,
            'isDestroyed' =>  $isDestroyed,
            'areTargetsDestroyed' => $areTargetsDestroyed,
            'winner' => $winner, // obsolet?
            'winners_name' => $winners_name,
       );

       return $response;
       
    }

    public function init() {
        $this->prepareData();
        $player = 0;

        

        if($this->user_id == auth()->user()->id) {
            $player = 1;
            $enemy = 2;
       } elseif($this->user2_id == auth()->user()->id) {
            $player = 2;
            $enemy = 1;
       }  

       $yourturn = false;
       if($this->data->status->shooter == $player) {
        $yourturn = true;
       } 
       
       $winners_name = '';
       if($this->data->status->finished) {
        if($this->data->status->winner == 1) {
            $user = User::find($this->user_id);
        } else {
            $user = User::find($this->user2_id);
        }

        $winners_name = $user->name;
       }

       $response = array(
            'status' => $this->data->status,
            'yourturn' => $yourturn,
            'ownShots' => $this->data->shots->{$player},
            'enemyShots' => $this->data->shots->{$enemy},
            'ownTargets' => $this->data->targets->{$player},
            'winners_name' => $winners_name
       );

       return $response;
    }

    public function ping() {
        $this->prepareData();
        $player = 0;
        $enemy = 0;
        if($this->user_id == auth()->user()->id) {
            $player = 1;
            $enemy = 2;
        } elseif($this->user2_id == auth()->user()->id) {
            $player = 2;
            $enemy = 1;
        }  

        $yourturn = false;
        if($this->data->status->shooter == $player) {
            $yourturn = true;
        } 

        $lastenemyshot = end($this->data->shots->{$enemy});

        $winners_name = '';
        if($this->data->status->finished) {
         if($this->data->status->winner == 1) {
             $user = User::find($this->user_id);
         } else {
             $user = User::find($this->user2_id);
         }
 
         $winners_name = $user->name;
        }

        $response = array(
            'yourturn' => $yourturn,
            'winner' => $this->data->status->winner,
            'finished' => $this->data->status->finished,
            'lastenemyshot' => $lastenemyshot,
            'winners_name' => $winners_name,
        );

        return $response;
    }
}



