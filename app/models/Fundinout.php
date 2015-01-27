<?php

class Fundinout extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'fundinout';

	protected $fillable = array('wallet_id', 'onbehalf','credits','description','fundtype');

	protected $guarded = array('id');

	public $timestamps = true;

	public function account_details()
	{
		return $this->belongsto('User','onbehalf');
	}
}