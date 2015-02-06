<?php

class Gamechannel extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_channel';

	protected $fillable = array(
		'channel_id',
		'game_id', 
		'operator_id',
		'channel_status'
	);

	protected $guarded = array('id');
	
	public $timestamps = true;

	public function gamedetails()
	{
		return $this->belongsto('Games','game_id');
	}

	public function operator()
	{
		return $this->belongsto('User','operator_id')->select('id','username','fullname','email');
	}

	public function bets()
	{
		return $this->hasMany('Gamebets','channel_id','id');
	}

}