<?php

if(!class_exists('WI_TrustCard_Response'))
{
	class WI_TrustCard_Response extends WI_Trustcard
	{
		function __construct($data, $secure_key)
		{
			$this->status = self::INIT;
			
			$this->required_fields = array('REF', 'RES');
			$this->optional_fields = array('AID', 'TYP', 'AMT', 'CUR', 'TID', 'PID', 'OID', 'TSS', 'SIG', 'CardID', 'CardMask', 'CardExp', 'AuthNumber', 'CardRecTxSec', 'TSS2', 'SIG2');
			
			$this->fields = array();
			
			// load req fields
			foreach($this->required_fields as $field)
			{
				if(isset($data[$field]))
					$this->fields[$field] = $data[$field];				
			}
			
			// load opt fields
			foreach($this->optional_fields as $field)
			{
				if(isset($data[$field]))
					$this->fields[$field] = $data[$field];
			}
			
			$this->validateFields();
			$this->validateSign($secure_key);
		}
		
		protected function getSignatureBase() 
		{		
			return $this->fields['AID'].$this->fields['TYP'].$this->fields['AMT'].$this->fields['CUR'].$this->fields['REF'].$this->fields['RES'].$this->fields['TID'].$this->fields['OID'].$this->fields['TSS'].$this->fields['CardID'].$this->fields['CardMask'].$this->fields['CardExp'].$this->fields['AuthNumber'].$this->fields['CardRecTxSec'].$this->fields['TSS2'];
		}
		
		protected function validateFields()
		{
			foreach($this->required_fields as $field)
			{
				if(!isset($this->fields[$field]))
				{
					$this->status = self::INCOMPLETE;
					return;
				}
			}
			
			if(!isset($this->fields['RES']) || $this->fields['RES']=='')
				$this->status = self::NOT_VALID;
					
			if(isset($this->fields['TSS']) && $this->fields['TSS']!='Y')
				$this->status = self::NOT_VALID;
					
			if($this->status==self::INIT)
				$this->status = self::VALID;
			
		}
		
		protected function validateSign($secret)
		{
			if(empty($secret))
				return;
				
	        if($this->status!=self::VALID)
	        	return;
			
			if(empty($this->fields[self::SIGN_FIELD2]))
	        {
				$this->status = self::SIGN_NOT_SIGNED;
				$this->result = self::RES_NOTSIGNED;
	        }
	        else
	        {				
				$sb = $this->getSignatureBase();
						
				$msg = pack('A*', $sb);
		        $key = pack('A*', $secret);
				
				$sign = hash_hmac('sha256', $msg, $key);		
				$sign = strtoupper($sign);
			
				if($this->fields[self::SIGN_FIELD2]!=$sign)
				{
					$this->status = self::SIGN_NOT_VALID;
					$this->result = self::RES_FAILED;
					return;
				}
			}

			// 0 - success, 1 - pending, 2 - announced, 3 - authorized
	        // TrustCard error ranges: 600-700, 2000-3000
	        // authorized (RES 3) is RES_OK state for card payments, 0 is RES_OK on redirect with not SIGN2
	        
			if($this->fields['RES']=='3')
			{
				if($this->status==self::SIGN_NOT_SIGNED)
					return;

				$this->result = self::RES_OK;
			}
			else if($this->fields['RES']=='1' || $this->fields['RES']=='2' || $this->fields['RES']=='0' || $this->fields['RES']=='4')
			{
				if($this->status==self::SIGN_NOT_SIGNED)
					return;

				$this->result = self::RES_TO;
			}
			else
				$this->result = self::RES_FAILED;
		}
	}
}