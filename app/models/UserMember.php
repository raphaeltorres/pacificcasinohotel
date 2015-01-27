<?php

class UserMember extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'acl_user_member';

	protected $fillable = array('user_id', 'group_id','date_created');

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function group()
	{
		return $this->belongsTo('Group', 'group_id', 'id');
	}

	public function wallet()
	{
		return $this->hasOne('Wallet', 'account_id' , 'user_id');
	}

}
	