<?php

class Wallet extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'wallet';

	protected $fillable = array('account_id', 'credits');

	protected $guarded = array('id');

	public $timestamps = true;

	public function account_credits()
	{
		return $this->belongsto('User','account_id');
	}
}
