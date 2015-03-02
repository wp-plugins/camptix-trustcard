<?php

if(!class_exists('WI_Trustcard'))
{ 		
	class WI_Trustcard
	{		
		const INIT = 'INIT';
		const VALID = 'VALID';
		const NOT_VALID = 'NOT_VALID';
		const INCOMPLETE = 'INCOMPLETE';
		const SIGNED = 'SIGNED';
		
		const SIGN_NOT_SIGNED = 'SIGN_NOT_SIGNED';
		const SIGN_NOT_VALID = 'SIGN_NOT_VALID';
		
		const RES_NOTSIGNED = 'RES_NOTSIGNED';
		const RES_FAILED = 'RES_FAILED';
		const RES_TO = 'RES_TO';
		const RES_OK = 'RES_OK';

		const SIGN_FIELD = 'SIG';
		const SIGN_FIELD2 = 'SIG2';
		
		protected $required_fields;
		protected $optional_fields;

		protected $store_key;
	    protected $is_test;
		protected $fields;
		protected $status;
		protected $result;
		
		function getResult()
		{
		    return $this->result;
		}

		public function getStatus()
		{
			return $this->status;
		}
		
		public function __get($name)
	    {
	        if(isset($this->fields[$name]))
	            return $this->fields[$name];

	        return null;
	    }

	    public function __isset($name)
	    {
	        return isset($this->fields[$name]);
	    }

	    function toArray()
	    {
	        return $this->fields;
	    }

	    static function get_valid_currencies()
	    {
	    	return array(
	    		'EUR' => 'EUR',
                'CZK' => 'CZK',
                'GBP' => 'GBP',
                'HUF' => 'HUF',
                'PLN' => 'PLN',
                'USD' => 'USD',
                'RON' => 'RON',
                'BGN' => 'BGN',
                'HRK' => 'HRK',
                'LTL' => 'LTL',
                'TRY' => 'TRY'
	    	);
	    }

	    static function get_valid_languages()
	    {
			return array(
				'cs' => 'cs',
				'de' => 'de',
				'en' => 'en',
				'es' => 'es',
				'hr' => 'hr',
				'hu' => 'hu',
				'it' => 'it',
				'pl' => 'pl',
				'ro' => 'ro',
				'ru' => 'ru',
				'sk' => 'sk',
				'sl' => 'sl',
				'uk' => 'uk'
			);
	    }
	}
}